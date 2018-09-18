import React from 'react';
import { followingSearch$ } from '../../services/FollowTester';

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

const FollowingBox = (props) => {
  return (
    <div className="card mb-4 box-shadow">
      <div className="card-header">
        <h4 className="my-0 font-weight-normal">Following</h4>
      </div>
      <div className="card-body text-left">
        <div className="input-group">
          <input className="form-control" id="followedProf" name="followedProf"
                 placeholder="Search among I'm following..." type="text" autoComplete="off"
                 onInput={(ev) => followingSearch$.next(ev.target.value)} />
        </div>
        <div className="row mt-3 pl-2 small">{ list(props.followingList) }</div>
      </div>
    </div>
  )
};

export default FollowingBox;