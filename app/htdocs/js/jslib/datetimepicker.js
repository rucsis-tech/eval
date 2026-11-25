function createDatetimepicker(options) {
    const group = document.getElementById(options.id);
    if (!group) return;

    group.setAttribute('data-target-input', "nearest")

    const ids = '#' + options.id;

    let clock = options.clock
    if (!clock) clock = group.getAttribute('data-clock')
    if (!clock || clock === "0") {
        clock = false
    } else {
        clock = true
    }
    const format = clock ? 'yyyy/MM/dd HH:mm' : 'yyyy/MM/dd'

    let name = options.name
    if (!name) name = group.getAttribute('data-name')
    if (!name) name = options.id

    if (!options.form) options.form = group.getAttribute('data-form')

    if (options.caption) {
        const label = document.createElement('label');
        label.setAttribute('class', 'pt-2 pr-2')
        label.innerText = options.caption
        group.append(label)
    }

    let input = document.querySelector(ids + ' input');
    if (!input) {
        input = document.createElement('input');
        input.setAttribute('type', 'text')
        input.setAttribute('name', name)
        input.setAttribute('class', 'form-control datetimepicker-input')
        input.setAttribute('value', group.getAttribute('data-value'))
        if (options.form) input.setAttribute('form', options.form)
        group.append(input)
    }
    input.setAttribute('data-target', ids);

    let div = document.querySelector(ids + ' div');
    if (!div) {
        div = document.createElement('div');
        div.innerHTML = '<i class="far fa-calendar-alt"></i>'
        div.setAttribute('class', 'input-group-text')
        div.setAttribute('data-toggle', 'datetimepicker')
        group.append(div)
    }
    div.setAttribute('data-target', ids);

    return new tempusDominus.TempusDominus(group, {
        localization: {
            format: format,
            dayViewHeaderFormat: {year: 'numeric', month: 'long'},
            today: '本日',
            close: '閉じる',
            selectMonth: '月を選択',
            previousMonth: '前月',
            nextMonth: '次月',
            selectYear: '年を選択',
            previousYear: '前年',
            nextYear: '次年',
            selectDecade: '期間を選択',
            previousDecade: '前期間',
            nextDecade: '次期間',
            previousCentury: '前世紀',
            nextCentury: '次世紀',
            pickHour: '時間を取得',
            incrementHour: '時間を増加',
            decrementHour: '時間を減少',
            pickMinute: '分を取得',
            incrementMinute: '分を増加',
            decrementMinute: '分を減少',
            pickSecond: '秒を取得',
            incrementSecond: '秒を増加',
            decrementSecond: '秒を減少',
            toggleMeridiem: '午前/午後切替',
            selectTime: '時間を選択',
        },
        display: {
            components: {clock: clock},
            buttons: {close: true,},
        }
    });

}
