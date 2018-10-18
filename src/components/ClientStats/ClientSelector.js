import React from 'react';
import { filterUserStatsKeystroke$ } from '../../services/ClientStats';

const ClientSelector = (props) => {
    return (
        <div className="card mb-4 box-shadow">
            <div className="card-header">
                <h4 className="my-0 font-weight-normal">Dumbu Server</h4>
            </div>
            <div className="card-body">
                <div className="input-group-lg">
                    <input type="text" id="clientName" name="clientName"
                           className="form-control" placeholder="Type client name and press ENTER..."
                           required="" autoComplete="off"
                           disabled={!props.hasStats}
                           onKeyUp={(ev) => filterUserStatsKeystroke$.next(ev)} />
                </div>
            </div>
        </div>
    );
};
    
export default ClientSelector;