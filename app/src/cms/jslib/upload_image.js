/**
 * 画像アップロード処理
 * @param button
 * @returns {Promise<void>}
 */
async function upload_image(button) {
    const input = button.parentNode.querySelector('input[type="file"]');
    const res = await image_post('/cms/image/tmp', input);
    if (res['tmp']) {
        const img = button.parentNode.parentNode.querySelector('img');
        const src = '/images/tmp/' + res['tmp'];
        img.setAttribute('src',src);
        set_image_hidden(button.parentNode, res['tmp'], res['origin']);
        return src;
    }
    return '';
}

/**
 * POST送信情報のImage更新関連hiddenに値を設定する
 * @param parentNode
 * @param tmp
 * @param origin
 */
function set_image_hidden(parentNode, tmp, origin) {
    const image_tmp = parentNode.querySelector('*[class="image_tmp"]')
    image_tmp.value = tmp;
    const image_origin = parentNode.querySelector('*[class="image_origin"]')
    image_origin.value = origin;
}

/**
 * 画像を送信し、一時ファイルパスを取得
 * @param url
 * @param input_file
 * @returns {Promise<any>}
 */
function image_post(url, input_file) {
    const files = input_file.files;
    if (!files || files.length === 0) {
        alert('ファイルが選択されていません。');
        return;
    }
    const postData = new FormData();
    postData.append('file', files[0]);

    return fetch(url, {
        method: 'POST',
        body: postData
    }).then((response) => {
        return response.json();
    }).then((json) => {
        return json;
    }).catch((e) => {
        console.log(e);
    });
}