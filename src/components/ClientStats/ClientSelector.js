import React from 'react';
import { filterUserStatsKeystroke$ } from '../../services/ClientStats';

const ClientSelector = (props) => {
    return (
        <div className="card mb-4 box-shadow">
            <div className="card-header">
                <h4 className="my-0 font-weight-normal">Client / Interval</h4>
            </div>
            <div className="card-body">
                <fieldset disabled={!props.hasStats}>
                    <div className="input-group-lg">
                        <input type="text" id="clientName" name="clientName"
                            className="form-control" placeholder="Type client name and press ENTER..."
                            required="" autoComplete="off"
                            onKeyUp={(ev) => filterUserStatsKeystroke$.next(ev)} />
                    </div>
                    <div className="input-group pl-4 pt-2">
                        <div className="col-6">
                            <input type="radio" class="form-check-input" name="period"
                                   value="month" id="month-stats"
                                   onClick={() => props.periodSelector.next('month')} />
                            <label className="form-check-label small" htmlFor="month-stats">Complete month</label>
                        </div>
                        <div className="col-6">
                            <input type="radio" class="form-check-input" name="period"
                                   value="all" id="all-stats"
                                   onClick={() => props.periodSelector.next('year')} />
                            <label className="form-check-label small" htmlFor="all-stats">All year</label>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    );
};
    
export default ClientSelector;