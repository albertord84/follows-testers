import React from 'react';
import { map, uniqueId } from 'lodash-es';

const statsList = (stats) => {
    return map(stats, stat => {
        const key = uniqueId(new Date().getTime());
        return (
            <div key={key} className="row mb-1 border-bottom">
                <div className="col-12"><b>Client ID: {stat.client_id}</b></div>
                <div className="col-12 text-muted"><small>Reference: {stat.ref_prof}</small></div>
                <div className="col-12 text-secondary">
                    <small><b>Followed: {stat.followed}</b></small>
                </div>
                <div className="col-12 text-secondary">
                    <small>Día: {stat.date} / {stat.time}</small>
                </div>
            </div>
        );
    });
}

const StatsPanel = (props) => {
    return (
        <div className="card mb-4 box-shadow">
            <div className="card-header">
                <h4 className="my-0 font-weight-normal">Stats</h4>
            </div>
            <div className="card-body pl-5 pr-5">{statsList(props.stats)}</div>
        </div>
    );
};
    
export default StatsPanel;