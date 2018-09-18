import React from "react";

import AccountData from "./AccountData";
import UserSearch from "./UserSearch";
import FollowersBox from "./FollowersBox";
import FollowingBox from "./FollowingBox";

const Form = (props) => {
  return (
    <form onSubmit={() => console.log('onSubmit')} className="follow-form">
      <fieldset disabled={props.waiting}>
        <div className="row justify-content-center">
          <div className="col-12 mb-3 text-center">
            <div className="row justify-content-center">
              <div className="col-5">
                <AccountData userName={props.userName}
                            password={props.password} />
              </div>
              <div className="col-5">
                <UserSearch users={props.users} />
              </div>
            </div>
            <div className="row justify-content-center">
              <div className="col-5">
                <FollowersBox followersList={props.followersList} />
              </div>
              <div className="col-5">
                <FollowingBox followingList={props.followingList} />
              </div>
            </div>
          </div>
        </div>
      </fieldset>
    </form>
  )
}

export default Form;
