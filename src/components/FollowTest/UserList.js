import React from 'react';
import { uniqueId } from "lodash-es";
import { onFollowUser$ } from '../../services/FollowTester';

const getName = (user) => {
  return user.username ||
         user.userName ||
         user.name ||
         user;
}

const UserList = (props) => {
  const data = props.users;
  const iterator = (user) => {
    const id = uniqueId() * new Date().getTime();
    const name = getName(user);
    return  (
      <div className="row user rounded" key={id}
           onClick={() => onFollowUser$.next(name)}>
        <div className="col-12">
          <span>{name}</span>
          <div className="float-right follow"><span className="badge-light text-gray small rounded pl-2 pr-2">follow</span></div>
        </div>
      </div>
    )
  }
  return (
    data.length === 0 ? '' :
    <div className="list bg-white border rounded pl-3 pr-3 pb-1 pt-1 w-100 text-left">
      { data.map(iterator) }
    </div>
  )
};

export default UserList;