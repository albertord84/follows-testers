import React from 'react';
import { map, uniqueId } from 'lodash-es';
import { logDateSelect$ } from '../../services/ClientStats';

const dateList = (dates) => {
    return map(dates, date => {
        const key = uniqueId(new Date().getTime());
        return (
            <span key={key} className="badge badge-secondary m-1 p-2 m-pointer"
                  onClick={(ev) => logDateSelect$.next(ev.target)}>{date}</span>
        );
    });
}

const StatDates = (props) => {
    return (
        <div className="row mt-3 pl-2">{dateList(props.dates)}</div>
    );
};

export default StatDates;