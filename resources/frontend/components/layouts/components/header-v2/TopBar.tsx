import { useTranslation } from 'react-i18next';
import { IoIosArrowDown } from 'react-icons/io';
import { Link } from 'react-router-dom';
import { useAuth } from '../../../../store/features/auth/authSlice';
import { ILanguage } from '../../../../types';
import { translate } from '../../../../utils/translate';
import LocationToggler from './LocationToggler';

const TopBar = () => {
    const { language } = useAuth();
    const { i18n } = useTranslation();

    const switchLanguage = (language: ILanguage) => {
        i18n.changeLanguage(language.code);
        window.location.reload();
    };

    return (
        <div className="max-lg:hidden border-b-[.5px] border-black/[.12] bg-theme-primary text-white h-9 flex items-center justify-center">
            <div className="container flex justify-between items-center gap-10">
                <div>
                    <LocationToggler />
                </div>

                <div className="flex items-center justify-center gap-5">
                    <div
                        className="option-dropdown max-md:hidden min-w-[100px]"
                        tabIndex={0}
                    >
                        <div className="option-dropdown__toggler no-style flex items-center gap-2 bg-transparent pr-3">
                            <span>
                                <img
                                    src={`/images/flags/${language?.flag}.png`}
                                    alt={language?.name}
                                    className="w-[14px]"
                                />
                            </span>
                            <span>{language?.name}</span>

                            <span className="option-dropdown__toggler--icon">
                                <IoIosArrowDown />
                            </span>
                        </div>

                        <div className="option-dropdown__options">
                            <ul>
                                {window.config.languages.map((lang) => (
                                    <li key={lang.code}>
                                        <button
                                            onClick={() => switchLanguage(lang)}
                                            className="option-dropdown__option px-3 py-1.5 rounded-sm"
                                        >
                                            <span>
                                                <img
                                                    src={`/images/flags/${lang.flag}.png`}
                                                    alt={lang.name}
                                                    className="w-[14px]"
                                                />
                                            </span>
                                            <span>{lang.name}</span>
                                        </button>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    </div>

                    {window.config.generalSettings.appMode == 'multiVendor' && (
                        <Link to={'/seller/signup'} className="max-md:hidden">
                            Be a Seller
                        </Link>
                    )}

                    <Link to={'/orders/track'} className="">
                        {translate('Track Order')}
                    </Link>
                </div>
            </div>
        </div>
    );
};

export default TopBar;
