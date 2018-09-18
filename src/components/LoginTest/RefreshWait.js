import React from 'react';

const RefreshWait = (props) => {
  if (props.visible) {
    return (
      <div className="row">
        <div className="col-12 text-center text-black-50">
          <h1 className="display-3">Wait...</h1>
        </div>
      </div>
    )
  }
  return '';
}

export default RefreshWait;