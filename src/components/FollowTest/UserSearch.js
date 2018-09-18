import React from 'react';
import UserList from "./UserList";
import { searchUserInput$ } from '../../services/FollowTester';

const UserSearch = (props) => {
  return (
    <div className="card mb-4 box-shadow">
      <div className="card-header">
        <h4 className="my-0 font-weight-normal">Search Instagram users</h4>
      </div>
      <div className="card-body">
        <div className="input-group autocomplete">
          <input onInput={(ev) => searchUserInput$.next(ev.target.value)}
                 className="form-control" id="newFollowing" name="newFollowing"
                 placeholder="Search new users to follow them..." type="text"
                 autoComplete="off" />
          <UserList users={props.users} />
        </div>
      </div>
    </div>
  )
};

export default UserSearch