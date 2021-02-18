let moduleClassIdList = []
let sender

const moduleClassId = $('#module-class-id')
const fieldList = {
    title: 'Tiêu đề',
    content: 'Nội dung',
    typez: 'Loại thông báo'
}

//  Get data from database
async function fetchData() {
    const baseUrl = '../../../api-v2/manage/get_module_class.php'
    const init = {
        method: 'GET',
        cache: 'no-cache'
    }

    let response = await fetch(baseUrl, init)
        .then((response) => response.json())

    return response
}

async function loadData() {
    let data = await fetchData()

    data.forEach((row, index) => {
        moduleClassIdList.push({id: index, text: row['ID_Module_Class']})
    })
}

document.addEventListener('DOMContentLoaded', async () => {
    const CustomSelectionAdapter = $.fn.select2.amd.require('select2/selection/customSelectionAdapter')

    sender = await getSender()
    await loadData()
    document.getElementById('submit').addEventListener('click', trySendNotification)

    //  Display selected tags
    moduleClassId.select2({
        data: moduleClassIdList,
        selectionAdapter: CustomSelectionAdapter,
        allowClear: false,
        selectionContainer: $('#list'),
        theme: 'bootstrap4'
    })
})

/*_________________________________________________*/

async function getSender() {
    const baseUrl = '../../shared/session.php?var=department_id'
    const init = {
        method: 'GET'
    }

    let response = await fetch(baseUrl, init)
        .then((response) => response.json())

    return response
}

function getClassList() {
    let selectedId = moduleClassId.val()

    if (selectedId.length === 0) {
        return
    }

    let selectedClasses = selectedId.map((_class) => moduleClassIdList[_class].text)
    return selectedClasses
}
const varToString = varObj => Object.keys(varObj)[1]
//  Display error if there are some unfulfilled fields
function canPostData(data) {
    for (const [field, fieldValue] of Object.entries(data.info)) {
        if (fieldValue === '') {
            alertify.error(`Trường "${fieldList[field]}" không được để trống!`)
                .delay(3)
                .dismissOthers()

            return false
        }
    }

    if (data.class_list === undefined) {
        alertify.error('Trường "Mã học phần" không được để trống!')
            .delay(3)
            .dismissOthers()

        return false
    }

    return true
}

/*_________________________________________________*/

//  Send notification info
async function postData(data) {
    const url = '../../../api-v2/manage/module_class_notification.php'

    const init = {
        method: 'POST',
        cache: 'no-cache',
        body: JSON.stringify(data)
    }

    const response = await fetch(url, init)
        .then((response) => response.json())

    return response
}

function trySendNotification() {
    const data = {
        info: {
            title: $('#title').val(),
            content: $('#content').val(),
            typez: 'Type',
            sender: sender
        },
        class_list: getClassList()
    }

    if (canPostData(data)) {
        postData(data).then((response) => {
            if (response.toString() === 'OK') {
                alertify.confirm('Thêm thông báo thành công!')
                    .setHeader('<i class="fas fa-info-circle"></i> Thông tin')
                    .setting({
                        'labels':
                            {
                                ok: 'Tạo thông báo mới',
                                cancel: 'Về trang chủ'
                            },
                        'defaultFocusOff': true,
                        'maximizable': false,
                        'movable': false,
                        'pinnable': false,
                        'onok': () => window.location.reload(),
                        'oncancel': () => window.location.replace(('../../home/'))
                    })
            }
            else {
                alertify.error('Có lỗi đã xảy ra, hãy thử lại sau!')
                    .delay(3)
                    .dismissOthers()
            }

            document.getElementById('submit').removeEventListener('click', trySendNotification)
        })
    }
}
