function postJsonRequest(url, data) {
    if (!data.noDisplay) loadStart();

    return fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
        .then((response) => {
            loadEnd();
            return response.json()　//ここでBodyからJSONを返す
        })
        .then((result) => {
            console.log(url, data, result)
            const status = result.status;
            const message = result.message;
            return result.response;
        })
        .catch((e) => {
            loadEnd();
            console.log(e)  //エラーをキャッチし表示
        })
}

function postJsonLoading() {

}

function postJsonError() {

}

function form_post(url, data) {
    const form = document.createElement('form');

    form.method = 'POST';
    form.action = url;

    for (let key in data) {
        const request = document.createElement('input');
        request.setAttribute('type', 'hidden'); //入力フォーム非表示
        request.name = key;
        request.value = data[key];
        form.appendChild(request);
    }

    document.body.appendChild(form);
    form.submit();
}

function CheckConfirm(msg) {
    if (!msg) msg = '削除'
    if (confirm(msg + 'しますか？')) {
        return true;
    } else {
        return false;
    }
}

var loadingCount = 0;

function loadStart() {
    loadingCount++;
    if (loadingCount == 1) {
        const loading = document.getElementById('loading');
        if (loading) {
            loading.classList.remove("loaded");
            loading.setAttribute('style', 'visibility:visible');
        }
    }

}

function loadEnd() {
    loadingCount--
    if (loadingCount == 0) {
        const loading = document.getElementById('loading');
        if (loading) {
            loading.classList.add("loaded");
            setTimeout(() => {
                loading.setAttribute('style', 'visibility:hidden');
            }, 100)
        }
    }

}


function disableSubmit(button) {
    setTimeout(() => {
        button.setAttribute('disabled', true);
    })
}


