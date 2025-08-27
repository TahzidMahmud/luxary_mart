import React from 'react';

interface Props {
    open: boolean;
    onClick?: () => void;
}

const Overlay = ({ open, onClick }: Props) => {
    return (
        <div
            className={`fixed inset-0 bg-black/[.6] z-[4] ${
                open ? 'opacity-100 visible' : 'opacity-0 invisible'
            } transition-all duration-300`}
            onClick={onClick}
        ></div>
    );
};

export default Overlay;
