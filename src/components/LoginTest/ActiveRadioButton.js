import React from "react";
import { assign } from "lodash-es";
import { loginTestActivated$ } from "../../services/LoginTester"

const RadioButton = (props) => {
  const elemProps = {
    type: 'radio', name: 'activated', id: props.id,
    autoComplete: 'off', value: props.value
  }
  const finalProps = props.active ?
    assign(elemProps, { defaultChecked: true }) :
    elemProps;
  const radioButton = React.createElement('input', finalProps);
  return radioButton;
}

const ActiveRadioButton = (props) => {
  const clsActive = "btn btn-outline-primary active";
  const clsInactive = "btn btn-outline-primary";
  const active = props.activated === 'on';
  return (
    <div className="ml-1 input-group-prepend">
      <div className="btn-group btn-group-toggle" data-toggle="buttons">
        <label className={active ? clsActive : clsInactive}
               onClick={(ev) => loginTestActivated$(ev.target)}>
          <RadioButton id="activated1" value="on" active={active} />
          <small>On</small>
        </label>
        <label className={active ? clsInactive : clsActive}
               onClick={(ev) => loginTestActivated$(ev.target)}>
          <RadioButton id="activated2" value="off" active={!active} />
          <small>Off</small>
        </label>
      </div>
    </div>
  )
}

export default ActiveRadioButton;
