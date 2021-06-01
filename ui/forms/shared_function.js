export async function getSender() {
    try {
        const baseUrl = '../../shared/session.php?var=id_account'
        const init = {
            method: 'GET',
            cache: 'no-cache'
        }

        const response = await fetch(baseUrl, init)
        const responseJson = await response.json()

        return responseJson

    } catch (e) {
        console.log(e)
    }
}

export function autoFillTemplate(templateNoti) {
    title.value = templateNoti.title
    type.selectedIndex = templateNoti.typez
    content.value = templateNoti.content
}

//  Get data from database
export async function fetchData(url) {
    const init = {
        method: 'GET',
        cache: 'no-cache'
    }

    let response = await fetch(url, init)
    let responseJson = await response.json()

    return responseJson
}

export async function postData(url, data) {
    const init = {
        method: 'POST',
        cache: 'no-cache',
        body: JSON.stringify(data)
    }

    const response = await fetch(url, init)
    const responseJson = await response.json()

    return responseJson
}

export function changeStatusButton() {
    if (this.type === 'date') {
        document.getElementsByClassName(this.id)[0].classList.remove('disable')
    }
    else {
        this.classList.add('disable')
    }
}

export function resetInputDate() {
    let elemID = this.classList[2]
    document.getElementById(elemID).setAttribute('data-date', 'Invalid date')
}

