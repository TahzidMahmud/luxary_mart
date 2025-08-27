import React from 'react';
import { cn } from '../../../react/utils/cn';
import { useAppContext } from '../../Context';
import GoalPopup from './GoalPopup';

const PopupIndex = () => {
    const { popup, closePopup } = useAppContext();

    return (
        <>
            <GoalPopup />

            <div
                className={cn(
                    'fixed inset-0 bg-black/60 z-[2] transition-all',
                    {
                        'opacity-0 invisible': !popup.name,
                    },
                )}
                onClick={closePopup}
            ></div>
        </>
    );
};

export default PopupIndex;
