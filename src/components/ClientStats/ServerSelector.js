import React from 'react';

const ServerSelector = (props) => {
    return (
        <div className="card mb-4 box-shadow">
            <div className="card-header">
                <h4 className="my-0 font-weight-normal">Dumbu Server</h4>
            </div>
            <div className="card-body">
                <div className="input-group autocomplete">
                    <div className="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="serverOne" name="statServer" className="custom-control-input"/>
                        <label className="custom-control-label" htmlFor="serverOne">dumbu.one</label>
                    </div>
                    <div className="custom-control custom-radio custom-control-inline ml-4">
                        <input type="radio" id="serverPro" name="statServer" className="custom-control-input"/>
                        <label className="custom-control-label" htmlFor="serverPro">dumbu.pro</label>
                    </div>
                </div>
            </div>
        </div>
        );
    };
    
    export default ServerSelector;