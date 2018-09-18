import React from 'react';

const ReferenceProfile = (props) => {
  if (props.profile===null) { return ''; }
  const profile = props.profile;
  return (
    <div className="col-12 mt-3">
      <div className="row">
        <div className="">
          <img className="rounded-circle ref-prof-pic" src={profile.profile_pic_url} alt="profile" />
        </div>
        <div className="ml-3 pt-3">
          <p className="m-0">{profile.full_name}</p>
          <p className="m-0 small text-secondary">{profile.username}</p>
        </div>
        <div className="ml-auto pt-3">
          <span className="badge badge-secondary">{profile.byline}</span>
        </div>
      </div>
    </div>
  );
};

export default ReferenceProfile;