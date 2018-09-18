import React from 'react';
import { followerSearch$ } from '../../services/FollowTester';

const list = (items) => {
  if (items.length === 0) { return ''; }
  const iterator = (item, index) => {
    if (index <= 10) {
      return (
        <div className="col-12 mt-1" key={item.pk}>{item.username}</div>
      )
    }
  }
  return items.map(iterator);
}

const FollowersBox = (props) => {
  return (
    <div className="card mb-4 box-shadow">
      <div className="card-header">
        <h4 className="my-0 font-weight-normal">Followers</h4>
      </div>
      <div className="card-body text-left">
        <div className="input-group">
          <input className="form-control" id="followingProf"
                 name="followingProf" placeholder="Search through followers list..."
                 type="text" autoComplete="off"
                 onInput={(ev) => followerSearch$.next(ev.target.value)} />
        </div>
        <div className="row mt-3 pl-2 small">{ list(props.followersList) }</div>
      </div>
    </div>
  )
};

export default FollowersBox;