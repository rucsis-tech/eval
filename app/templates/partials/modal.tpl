{* キャンペーンモーダル（P1 / モーダル仕様モック準拠・表示内容は実装時にJSまたはサーバで差し込み） *}
<div class="modal-overlay hidden" id="campaignModal">
    <div class="modal">
        <span class="m-count" id="mCount" hidden></span>
        <span class="m-close" id="modalClose">×</span>
        <div class="m-visual" id="mVisual">
            <div>
                <span class="m-lbl" id="mLbl"></span>
                <h2 id="mTitle"></h2>
                <p id="mDesc"></p>
            </div>
        </div>
        <div class="m-foot">
            <a class="m-btn" href="#" id="mCta"></a>
            <a class="m-skip" href="#" id="modalSkip">閉じる</a>
            <div class="m-dots" id="mDots"></div>
        </div>
    </div>
</div>
