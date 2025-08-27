import { FaCircleUser } from 'react-icons/fa6';
import { IoIosArrowDown } from 'react-icons/io';
import { Link } from 'react-router-dom';

const TopBar = () => {
    return (
        <div className="border-b border-theme-primary/[.14] text-zinc-500 text-sm h-9 flex items-center justify-center">
            <div className="container grid grid-cols-4 items-center gap-10">
                <div>
                    <a href="tel:+02 000 555 999" className="">
                        Hotline: +02 000 555 999
                    </a>
                </div>

                <div className="col-span-2 flex items-center justify-center gap-10">
                    <div className="option-dropdown max-xl:hidden" tabIndex={0}>
                        <div className="option-dropdown__toggler no-style bg-white text-gray-700 border border-[#e3e3e3]">
                            <span>
                                <img
                                    src="/images/flags/en-US.png"
                                    alt=""
                                    className="w-[14px]"
                                />
                            </span>
                            <span>Eng</span>

                            <span className="option-dropdown__toggler--icon">
                                <IoIosArrowDown />
                            </span>
                        </div>

                        <div className="option-dropdown__options">
                            <ul>
                                <li>
                                    <a
                                        href=""
                                        className="option-dropdown__option"
                                    >
                                        <span>
                                            <img
                                                src="/images/flags/en-US.png"
                                                alt=""
                                                className="w-[14px]"
                                            />
                                        </span>
                                        <span>Eng</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <Link to={'/seller/login'} className="arm-h4">
                        Be a Seller
                    </Link>

                    <Link to={'/track-order'} className="arm-h4">
                        Track Order
                    </Link>
                </div>

                <div className="flex items-center justify-end">
                    <Link to={'/login'} className="flex items-center gap-2">
                        <span className="text-xl text-stone-300">
                            <FaCircleUser />
                        </span>
                        <span>Login</span>
                    </Link>
                    <span className="h-[11px] border-r border-zinc-500 mx-3"></span>
                    <Link to={'/registration'}>Registration</Link>
                </div>
            </div>
        </div>
    );
};

export default TopBar;
