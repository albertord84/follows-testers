import React from "react";

const LoginError = (props) => {
  if (props.error !== '') {
    return (
      <div className="alert alert-danger small text-center mt-3">
        {props.error}
      </div>
    )
  }
  return '';
}

export default LoginError;