/**
 * P8 マイページ（モック）
 */
(function () {
    'use strict';

    /* =========================================================
       マイページ P8（モック）
       - 🔒要ログイン。非会員はログイン/無料登録誘導ビュー
       - 状態込み設計：無料会員（プランなし→アップセルカード）／サブスク会員（プラン契約カード）
       - プラン契約カード：プラン名・適用中招待コード・月額・次回更新日・支払い方法・
         プラン変更ボタン＋解約リンク（※解約導線は隠さない方針＝信頼優先。
         解約時の引き止めオファーはバックエンド管理のキャンペーン枠として実装時に検討）
       - ポイント：残高・直近失効・購入導線・取得/消費履歴
       - 購入済み攻略ツール：直近の購入一覧→商品詳細へ
       - ソーシャル連携：LINE/Apple/Google/Xの連携状態管理（連携済み/連携する）
       - 通知設定：メール系トグルのみ（プッシュ通知はアプリ版第2段階で提供予定）
       - サポート：ガイド・ヘルプ・法務系リンク（ドロワーと同内容の再掲）
    ========================================================= */
    
    const PLANS = {
      sub_full: {name:'全機能使い放題プラン', price:9800, code:'EVALALL適用中', pay:'クレジットカード ****1234'},
      sub_chuo: {name:'中央競馬限定プラン', price:5980, code:null, pay:'クレジットカード ****1234'},
      amb:      {name:'プレミアム（全機能）', price:0, code:'アンバサダー特典', pay:'—', amb:true},
    };
    const PT_HISTORY = [
      {date:'8/9', desc:'ログインボーナス', amt:+10},
      {date:'8/8', desc:'初回限定パック購入', amt:+2000},
      {date:'8/7', desc:'推奨馬リスト（8/7分）購入', amt:-500},
      {date:'8/6', desc:'アンケート回答特典', amt:+50},
      {date:'8/2', desc:'前走不利情報（8/2分）購入', amt:-300},
    ];
    const TOOLS = [
      {name:'推奨馬リスト（8/7分）', sub:'8/7 購入・500pt', ico:'list'},
      {name:'前走不利情報（8/2分）', sub:'8/2 購入・300pt', ico:'flag'},
    ];
    const ICONS = {
      list:'<svg viewBox="0 0 24 24"><path d="M8 6h13M8 12h13M8 18h13"/><circle cx="3.5" cy="6" r="1.3"/><circle cx="3.5" cy="12" r="1.3"/><circle cx="3.5" cy="18" r="1.3"/></svg>',
      flag:'<svg viewBox="0 0 24 24"><path d="M4 21V4"/><path d="M4 5h13l-2.5 3.5L17 12H4"/></svg>',
    };
    
    /* ===== アンバサダー（2026-09-30追加） =====
       - 管理画面でユーザーに「アンバサダー」権限を付与 → 全機能（プレミアム相当）を無償で利用可＋マイページに報酬欄
       - 報酬：アンバサダー専用クーポンコードを使って課金したユーザーの売上×報酬率を永続的に支払う
         報酬率はアンバサダーごと・期間ごとに異なり得るため管理画面で管理。マイページには売上・報酬率を表示せず報酬額のみ表示（2026-10-01）
         ※モックの金額は報酬額を直接持たず「売上×rate」で算出しているだけ。実装時はサーバで確定した報酬額を返す
         （サブスク：初回決済＝新規／2回目以降の更新＝継続。紹介ユーザーのポイント購入も対象だが新規・継続の区別はしない）
       - 紹介ユーザー個人は特定できない形（プラン別の人数・金額のみ）で表示＝プライバシー配慮
       - プラン名・金額はすべて仮 */
    const AMB = {
      code:'UMAKEN10', rate:0.10, grantUntil:'2027/3/31', bank:'みずほ銀行 ****123',
      month:'2026年9月', period:'9/1〜9/30', closeNote:'10/10確定予定',
      activeUsers:90, newUsers:24,
      // 今月の内訳（plan×新規/継続）。人数＝当月に決済が発生したユーザー数
      plans:[
        {name:'プランA', n:{new:[12,12000], cont:[38,38000]}},
        {name:'プランB', n:{new:[9,6000],   cont:[21,14000]}},
        {name:'プランC', n:{new:[3,2400],   cont:[7,5600]}},
      ],
      pt:[18,12000], // ポイント購入（件数・売上）※新規・継続の区別なし
      // 月別推移（報酬額）：新規／継続
      history:[
        {m:'4月', ym:'2026年4月', new:1500, cont:0,    pt:300,  status:'done', pay:'5/31 振込済'},
        {m:'5月', ym:'2026年5月', new:1800, cont:1300, pt:500,  status:'done', pay:'6/30 振込済'},
        {m:'6月', ym:'2026年6月', new:2200, cont:2500, pt:800,  status:'done', pay:'7/31 振込済'},
        {m:'7月', ym:'2026年7月', new:1900, cont:3700, pt:900,  status:'done', pay:'8/31 振込済'},
        {m:'8月', ym:'2026年8月', new:2100, cont:4700, pt:1100, status:'proc', pay:'9/30 振込手続き中'},
        {m:'9月', ym:'2026年9月', new:2040, cont:5760, pt:1200, status:'calc', pay:'10/10確定・10/31振込予定'},
      ],
    };
    const STATUS_LBL = {done:'振込済', proc:'振込手続き中', calc:'集計中'};
    
    let status = 'free'; // 'guest' | 'free' | 'sub_full' | 'sub_chuo' | 'amb'
    let rwTab = 'all';
    const yen = n => n.toLocaleString();
    
    function render(){
      const guest = status === 'guest';
      document.getElementById('guestView').hidden = !guest;
      document.getElementById('memberView').innerHTML = guest ? '' : memberHTML();
      const balEl = document.querySelector('.h-point .p-num'); if (balEl) balEl.textContent = guest ? '−' : '7,777';
      if(!guest) bindToggles();
      if(status === 'amb') bindAmb();
    }
    
    function memberHTML(){
      const plan = PLANS[status];
      const badge = status === 'amb' ? '<span class="a-badge amb">アンバサダー</span>'
        : plan ? `<span class="a-badge sub">${status === 'sub_full' ? '全機能使い放題' : '中央競馬限定'}</span>`
        : '<span class="a-badge free">無料会員</span>';
    
      // プラン契約 or アップセル
      const planBlock = (plan && plan.amb) ? `
        <div class="plan-card amb">
          <div class="p-row1">
            <span class="p-name">${plan.name}</span>
            <span class="p-code">${plan.code}</span>
            <span class="p-price">¥0<small>（運営付与）</small></span>
          </div>
          <div class="p-meta">
            <span>付与期間：<b>〜${AMB.grantUntil}</b></span>
            <span>付与：<b>運営（アンバサダー契約）</b></span>
          </div>
          <p class="p-note">アンバサダー契約に基づき、全機能をご利用いただけます。付与期間の変更・終了は運営からご連絡します。</p>
        </div>
        ${ambassadorHTML()}` : plan ? `
        <div class="plan-card">
          <div class="p-row1">
            <span class="p-name">${plan.name}</span>
            ${plan.code ? `<span class="p-code">${plan.code}</span>` : ''}
            <span class="p-price">¥${yen(plan.price)}<small>/月（仮）</small></span>
          </div>
          <div class="p-meta">
            <span>次回更新：<b>9/8</b></span>
            <span>支払い方法：<b>${plan.pay}</b></span>
          </div>
          <div class="p-actions">
            <a class="p-change">プランを変更する</a>
            <a class="p-cancel">解約手続き</a>
          </div>
        </div>` : `
        <a class="up-card" href="/settle/settle_subsc">
          <p class="u-t">サブスクで<b>全レースの好走確率・リアルタイム期待値</b>をすべて開放</p>
          <div class="u-plans">
            <span class="u-chip">中央競馬限定</span>
            <span class="u-chip">地方競馬限定</span>
            <span class="u-chip">全機能使い放題</span>
          </div>
          <span class="u-cta">プランを見る ›</span>
        </a>`;
    
      return `
      <div class="acct-card">
        <span class="avatar">EV</span>
        <div>
          <p class="a-name">eval_user</p>
          <p class="a-mail">mr****@gmail.com</p>
        </div>
        ${badge}
      </div>
      ${planBlock}
    
      <div class="sec-title"><h2>ポイント</h2><span class="en">POINT</span><a class="more">履歴をすべて見る ›</a></div>
      <div class="pt-band">
        <span class="p-coin">P</span>
        <div>
          <p class="b-name">保有ポイント</p>
          <p class="b-num">7,777<small>pt</small></p>
          <p class="b-exp">有効期限が近いポイント：<b>500pt</b>（8/31まで）</p>
        </div>
        <a class="b-buy" href="/settle/settle_point">ポイント購入 ›</a>
      </div>
      <ul class="hist-list">${PT_HISTORY.map(h => `
        <li class="hist-row">
          <span class="h-date">${h.date}</span>
          <span class="h-desc">${h.desc}</span>
          <span class="h-amt ${h.amt > 0 ? 'plus' : 'minus'}">${h.amt > 0 ? '+' : ''}${yen(h.amt)}pt</span>
        </li>`).join('')}
      </ul>
    
      <div class="sec-title"><h2>購入済み攻略ツール</h2><span class="en">MY TOOLS</span><a class="more">一覧 ›</a></div>
      <ul class="tool-list">${TOOLS.map(t => `
        <li><a class="tool-row" href="/tools/tools_detail?id=t303">
          <span class="t-ico">${ICONS[t.ico]}</span>
          <span><span class="t-name">${t.name}</span><br><span class="t-sub">${t.sub}</span></span>
          <span class="t-chev">›</span>
        </a></li>`).join('')}
      </ul>
    
      <div class="sec-title"><h2>登録情報・連携</h2><span class="en">ACCOUNT</span></div>
      <ul class="set-list">
        <li><a class="set-row">メールアドレス<span class="s-sub">mr****@gmail.com</span><span class="s-chev">›</span></a></li>
        <li><a class="set-row">ニックネーム<span class="s-sub">eval_user</span><span class="s-chev">›</span></a></li>
        <li><a class="set-row">誕生日<span class="s-sub">未登録（登録で毎年ボーナスpt）</span><span class="s-chev">›</span></a></li>
        <li class="set-row"><span class="sns-badge line">L</span>LINE<span class="s-linked">連携済み</span></li>
        <li class="set-row"><span class="sns-badge apple"></span>Apple<span class="s-link-btn">連携する</span></li>
        <li class="set-row"><span class="sns-badge google">G</span>Google<span class="s-link-btn">連携する</span></li>
        <li class="set-row"><span class="sns-badge x">𝕏</span>X<span class="s-link-btn">連携する</span></li>
      </ul>
    
      <div class="sec-title"><h2>通知設定</h2><span class="en">NOTIFICATION</span></div>
      <ul class="set-list">
        <li class="set-row">お得なキャンペーン情報（メール）<span class="tgl on" data-tgl></span></li>
        <li class="set-row">重賞・注目レースのお知らせ（メール）<span class="tgl" data-tgl></span></li>
        <li class="set-row">ポイント有効期限のお知らせ（メール）<span class="tgl on" data-tgl></span></li>
      </ul>
      <p class="set-note">※ プッシュ通知はアプリ版（第2段階）で提供予定です。重要なお知らせ（契約・決済関連）は設定に関わらず送信されます。</p>
    
      <div class="sec-title"><h2>サポート</h2><span class="en">SUPPORT</span></div>
      <ul class="set-list">
        <li><a class="set-row" href="/pages/guide">初めての方ガイド<span class="s-chev only">›</span></a></li>
        <li><a class="set-row" href="/pages/faq">ヘルプ・お問い合わせ<span class="s-chev only">›</span></a></li>
        <li><a class="set-row" href="/pages/rules">利用規約<span class="s-chev only">›</span></a></li>
        <li><a class="set-row" href="/pages/privacy">プライバシーポリシー<span class="s-chev only">›</span></a></li>
        <li><a class="set-row" href="/pages/rules">資金決済法に基づく表示<span class="s-chev only">›</span></a></li>
        <li><a class="set-row" href="/pages/commercial_law">特定商取引法に基づく表示<span class="s-chev only">›</span></a></li>
        <li><a class="set-row" href="/pages/company">運営会社<span class="s-chev only">›</span></a></li>
      </ul>
    
      <div class="out-wrap">
        <a class="logout-btn">ログアウト</a>
        <a class="quit-link">退会手続きについて</a>
      </div>`;
    }
    
    const yenS = n => '¥' + Math.round(n).toLocaleString();
    function sumOf(kind){ // kind: 'new' | 'cont'（サブスク決済のみ）
      return AMB.plans.reduce((s, p) => s + p.n[kind][1], 0);
    }
    const histAll = h => h.new + h.cont + h.pt;
    function ambassadorHTML(){
      const sNew = sumOf('new'), sCont = sumOf('cont'), sPt = AMB.pt[1];
      const rNew = sNew * AMB.rate, rCont = sCont * AMB.rate, rPt = sPt * AMB.rate, rAll = rNew + rCont + rPt;
      const prev = AMB.history[AMB.history.length - 2];
      const prevAll = histAll(prev);
      const mom = Math.round((rAll - prevAll) / prevAll * 100);
      return `
      <div class="sec-title"><h2>アンバサダー報酬</h2><span class="en">REWARD</span></div>
      <div class="rw-hero">
        <div class="rh-top"><span class="rh-lbl">今月の報酬見込み</span><span class="rh-per">${AMB.month}（${AMB.period}）・${AMB.closeNote}</span></div>
        <p class="rh-num">${yenS(rAll)}<small>見込み</small><span class="rh-mom">前月比 +${mom}%</span></p>
        <div class="rw-split">
          <div class="rs"><p class="k"><i style="background:#5b8def"></i>新規</p>
            <p class="v">${yenS(rNew)}</p><p class="s">今月の新規<br><b>${AMB.newUsers}人</b></p></div>
          <div class="rs"><p class="k"><i style="background:#0fa878"></i>継続</p>
            <p class="v">${yenS(rCont)}</p><p class="s">紹介中の有効会員<br><b>${AMB.activeUsers}人</b></p></div>
          <div class="rs"><p class="k"><i style="background:#cf7f2a"></i>ポイント購入</p>
            <p class="v">${yenS(rPt)}</p><p class="s">紹介ユーザーの購入<br><b>${AMB.pt[0]}件</b></p></div>
        </div>
      </div>
    
      <div class="rw-chart">
        <div class="ch-h"><span class="ch-t">月別の報酬推移</span>
          <span class="lg"><span><i style="background:#0fa878"></i>継続</span><span><i style="background:#5b8def"></i>新規</span><span><i style="background:#cf7f2a"></i>pt購入</span></span></div>
        ${chartSVG()}
        <p class="tip" id="rwTip">棒をタップすると内訳を表示（* は集計中の見込み）</p>
      </div>
    
      <div class="rw-tabs" id="rwTabs">
        <a data-k="all" class="${rwTab === 'all' ? 'on' : ''}">合計</a>
        <a data-k="new" class="${rwTab === 'new' ? 'on' : ''}">新規</a>
        <a data-k="cont" class="${rwTab === 'cont' ? 'on' : ''}">継続</a>
      </div>
      ${breakdownHTML()}
      <p class="rw-note">※ 今月（${AMB.month}）の内訳。人数＝当月に決済が発生した紹介ユーザー数（個人は表示しません）。<b>ポイント購入は新規・継続の区別がないため「合計」にのみ計上</b>。プラン名・金額は仮。</p>
    
      <div class="code-card">
        <p class="cc-l">あなたの紹介コード</p>
        <div class="cc-row"><span class="cc-code">${AMB.code}</span>
          <span class="cc-btn" id="ccCopy">コピー</span><span class="cc-btn x">𝕏 シェア</span></div>
        <p class="cc-d">このコードでサブスクに申し込んだユーザーには<b>初月無料</b>などの特典が適用されます（特典内容は管理画面で設定）。コード経由のユーザーの決済は<b>永続的に</b>あなたの報酬対象になります。</p>
      </div>
    
      <div class="sec-title"><h2>報酬の履歴・振込</h2><span class="en">PAYMENT</span></div>
      <ul class="pay-list">${[...AMB.history].reverse().map((h, i) => `
        <li class="pay-row" data-i="${AMB.history.length - 1 - i}">
          <span class="pm">${h.ym.replace('2026年', '')}</span>
          <span class="pd">${h.pay}</span>
          <span class="pa">${yenS(histAll(h))}</span>
          <span class="ps ${h.status}">${STATUS_LBL[h.status]}</span>
          <span class="chev">›</span>
        </li>`).join('')}
      </ul>
      <ul class="set-list" style="margin-top:0">
        <li><a class="set-row">振込先口座<span class="s-sub">${AMB.bank}</span><span class="s-chev">›</span></a></li>
        <li><a class="set-row">報酬明細をダウンロード（CSV）<span class="s-chev only">›</span></a></li>
      </ul>
      <p class="rw-note">※ 月末締め・翌月10日確定・翌月末振込（仮）。確定額が1,000円未満の月は翌月に繰り越し（仮）。返金・チャージバックが発生した決済は対象外。</p>`;
    }
    function breakdownHTML(){
      const cols = rwTab === 'all' ? ['new', 'cont'] : [rwTab];
      const rows = AMB.plans.map(p => ({
        name:p.name,
        n:cols.reduce((s, k) => s + p.n[k][0], 0),
        y:cols.reduce((s, k) => s + p.n[k][1], 0),
      }));
      const withPt = rwTab === 'all'; // ポイント購入は新規・継続の区別なし＝合計タブのみ
      const tn = rows.reduce((s, r) => s + r.n, 0);
      const ty = rows.reduce((s, r) => s + r.y, 0) + (withPt ? AMB.pt[1] : 0);
      return `<table class="rw-table">
        <tr><th>プラン</th><th>人数</th><th>報酬</th></tr>
        ${rows.map(r => `<tr><td>${r.name}</td><td>${r.n}人</td><td class="rw">${yenS(r.y * AMB.rate)}</td></tr>`).join('')}
        ${withPt ? `<tr><td>ポイント購入<span class="sub">紹介ユーザーの購入</span></td><td>${AMB.pt[0]}件</td><td class="rw">${yenS(AMB.pt[1] * AMB.rate)}</td></tr>` : ''}
        <tr class="sum"><td>合計${withPt ? '' : `<span class="sub">サブスク${rwTab === 'new' ? '新規' : '継続'}分</span>`}</td><td>${tn}人</td><td class="rw">${yenS(ty * AMB.rate)}</td></tr>
      </table>`;
    }
    function chartSVG(){
      const W = 360, H = 150, padL = 34, padB = 20, padT = 14;
      const data = AMB.history, max = 10000;
      const bw = 30, gap = (W - padL - data.length * bw) / data.length;
      const y = v => padT + (H - padT - padB) * (1 - v / max);
      const grid = [0, 5000, 10000].map(v => `
        <line x1="${padL}" x2="${W}" y1="${y(v)}" y2="${y(v)}" stroke="#2a2e38" stroke-width="1"/>
        <text x="${padL - 6}" y="${y(v) + 3}" fill="#6b7384" font-size="9" text-anchor="end">${v ? (v / 1000) + '千' : '0'}</text>`).join('');
      // 積み上げ順（下から）：継続 → 新規 → ポイント購入。セグメント間は2pxの間隔、最上段の上端のみ角丸4px
      const SERIES = [['cont', '#0fa878'], ['new', '#5b8def'], ['pt', '#cf7f2a']];
      const bars = data.map((d, i) => {
        const x = padL + gap / 2 + i * (bw + gap);
        const cur = i === data.length - 1;
        const segs = SERIES.filter(([k]) => d[k] > 0);
        let base = 0, out = '';
        segs.forEach(([k, col], si) => {
          const y0 = y(base), y1 = y(base + d[k]);
          const bottom = y0 - (si > 0 ? 2 : 0);
          const top = y1;
          if(si === segs.length - 1){
            out += `<path d="M${x},${bottom} V${top + 4} Q${x},${top} ${x + 4},${top} H${x + bw - 4} Q${x + bw},${top} ${x + bw},${top + 4} V${bottom} Z" fill="${col}"/>`;
          }else{
            out += `<rect x="${x}" y="${top}" width="${bw}" height="${Math.max(0, bottom - top)}" fill="${col}"/>`;
          }
          base += d[k];
        });
        const tot = histAll(d);
        return `<g class="bar" data-i="${i}" style="cursor:pointer" ${cur ? 'opacity=".85"' : ''}>
          <rect x="${x - gap / 2}" y="${padT}" width="${bw + gap}" height="${H - padT - padB}" fill="transparent"/>
          ${out}
          <text x="${x + bw / 2}" y="${y(tot) - 4}" fill="${cur ? '#fff' : '#9aa3b2'}" font-size="9" font-weight="800" text-anchor="middle">${(tot / 1000).toFixed(1)}千</text>
          <text x="${x + bw / 2}" y="${H - 5}" fill="${cur ? '#fff' : '#9aa3b2'}" font-size="9.5" font-weight="${cur ? 900 : 700}" text-anchor="middle">${d.m}${cur ? '*' : ''}</text>
        </g>`;
      }).join('');
      return `<svg viewBox="0 0 ${W} ${H}" role="img" aria-label="月別の報酬推移（継続・新規・ポイント購入の積み上げ）">${grid}${bars}</svg>`;
    }
    function bindAmb(){
      document.querySelectorAll('#rwTabs a').forEach(a => a.addEventListener('click', () => { rwTab = a.dataset.k; render(); document.getElementById('rwTabs').scrollIntoView({block:'center'}); }));
      document.querySelectorAll('.rw-chart .bar').forEach(g => g.addEventListener('click', () => {
        const d = AMB.history[+g.dataset.i];
        document.getElementById('rwTip').innerHTML = `${d.ym}：<b>${yenS(histAll(d))}</b>（新規 ${yenS(d.new)}／継続 ${yenS(d.cont)}／pt ${yenS(d.pt)}）`;
      }));
      document.querySelectorAll('.pay-row').forEach(r => r.addEventListener('click', () => openPay(AMB.history[+r.dataset.i])));
      const cp = document.getElementById('ccCopy');
      if(cp) cp.addEventListener('click', () => { try{ navigator.clipboard.writeText(AMB.code); }catch(e){} cp.textContent = 'コピー済み'; });
    }
    function openPay(h){
      const all = histAll(h);
      document.getElementById('psTitle').textContent = h.ym + 'の報酬詳細';
      document.getElementById('psBody').innerHTML = `
        <tr><td>サブスク 新規（初回決済）分</td><td>${yenS(h.new)}</td></tr>
        <tr><td>サブスク 継続（2回目以降）分</td><td>${yenS(h.cont)}</td></tr>
        <tr><td>ポイント購入分</td><td>${yenS(h.pt)}</td></tr>
        <tr class="big"><td>報酬額</td><td>${yenS(all)}</td></tr>
        <tr><td>状況</td><td>${STATUS_LBL[h.status]}（${h.pay}）</td></tr>`;
      document.getElementById('paySheet').hidden = false;
    }
    document.getElementById('psClose').addEventListener('click', () => document.getElementById('paySheet').hidden = true);
    document.getElementById('paySheet').addEventListener('click', e => { if(e.target.id === 'paySheet') e.currentTarget.hidden = true; });
    
    function bindToggles(){
      document.querySelectorAll('[data-tgl]').forEach(t => t.addEventListener('click', () => t.classList.toggle('on')));
    }
    
    document.querySelectorAll('.mock-ctrl a').forEach(a => a.addEventListener('click', e => { e.preventDefault();
      status = a.dataset.st;
      document.querySelectorAll('.mock-ctrl a').forEach(x => x.classList.toggle('on', x === a));
      render();
    }));
    
    render();
})();
