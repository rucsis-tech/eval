function toggle_change(node) {
    const switchOuter = node;
    const toggleSwitch = switchOuter.querySelector('div');
    const disabled_state = switchOuter.querySelector('input');

    switchOuter.classList.toggle("active");
    toggleSwitch.classList.toggle("active");
    if (disabled_state.value == 0) {
        disabled_state.value = 1;
    } else {
        disabled_state.value = 0;
    }
    console.log(disabled_state.value);
}

let styleTag = document.createElement('style');
styleTag.textContent = `

        .switch_outer {
            width: 56px;
            height: 30px;
            background-color: lightgray;
            border-radius: calc(infinity * 1px);
            position: relative;
            cursor: pointer;
            transition: background-color .2s ease-in-out;
        }

        .switch_outer.active {
            background-color: #51E373;
        }

        .toggle_switch {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            position: absolute;
            background-color: white;
            top: 0;
            bottom: 0;
            left: 5px;
            margin: auto;
            transition: left .3s ease-in-out;
        }

        .toggle_switch.active {
            left: 30px;
        }

`;
document.head.appendChild(styleTag);