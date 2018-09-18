import React from 'react';
import { refProfileInput$ } from '../../services/Texting';
import ProfilesList from "./ProfilesList";
import ReferenceProfile from "./ReferenceProfile";

const ReferenceProfileData = (props) => {
  return (
    <div className="card mb-4 box-shadow">
      <div className="card-header">
        <h4 className="my-0 font-weight-normal">Reference Profile</h4>
      </div>
      <div className="card-body">
        <div className="input-group autocomplete">
          <div className="input-group-prepend">
            <span className="input-group-text">@</span>
          </div>
          <input className="form-control" id="userName"
                 placeholder="Instagram username" required=""
                 type="text" autoComplete="off" defaultValue={props.userName}
                 onInput={(e) => refProfileInput$.next(e.target.value)} />
          <ProfilesList profiles={props.searchList} />
          <ReferenceProfile profile={props.refProf} />
        </div>
      </div>
    </div>
  );
};

export default ReferenceProfileData;