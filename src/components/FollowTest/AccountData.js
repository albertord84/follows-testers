import React from "react";
import { credentialChange$ } from "../../services/FollowTester";

const AccountData = (props) => {
  return (
    <div className="card mb-4 box-shadow">
      <div className="card-header">
        <h4 className="my-0 font-weight-normal">Instagram Account</h4>
      </div>
      <div className="card-body">
        <div className="input-group">
          <div className="input-group-prepend">
            <span className="input-group-text">@</span>
          </div>
          <input className="form-control" id="userName" placeholder="Instagram username"
                 required="" type="text" autoComplete="off" defaultValue={props.userName}
                 onInput={(ev) => credentialChange$.next(ev.target)} />
        </div>
        <div className="input-group mt-3">
          <input className="form-control" id="password" placeholder="Password"
                 required="" type="password" defaultValue={props.password}
                 onInput={(ev) => credentialChange$.next(ev.target)} />
        </div>
      </div>
    </div>
  )
};

export default AccountData;