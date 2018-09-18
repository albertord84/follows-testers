import React from 'react';
import { map } from "lodash-es";
import { onRefProfileSelected$ } from "../../services/Texting";

const iterator = (profile) => {
  return (
    <div className="row user rounded" key={profile.pk}
         onClick={() => onRefProfileSelected$.next(profile.pk)}>
      <div className="col-12">
        <span>{profile.username}</span>
        <div className="float-right follow"><span className="badge-light text-gray small rounded pl-2 pr-2">select</span></div>
      </div>
    </div>
  )
}

const ProfilesList = (props) => {
  if (props.profiles.length === 0) { return ''; }
  return (
    <div className="list bg-white border rounded pl-3 pr-3 pb-1 pt-1 w-100 text-left">
      {map(props.profiles, iterator)}
    </div>
  )
};

export default ProfilesList;