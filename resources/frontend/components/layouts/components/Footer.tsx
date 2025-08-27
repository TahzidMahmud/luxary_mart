import { FormEvent, useState } from 'react';
import toast from 'react-hot-toast';
import { BsApple, BsGooglePlay } from 'react-icons/bs';
import { PiArrowRightLight } from 'react-icons/pi';
import {
    RiFacebookLine,
    RiInstagramLine,
    RiLinkedinLine,
    RiTwitterXLine,
    RiYoutubeLine,
} from 'react-icons/ri';
import { Link } from 'react-router-dom';
import * as yup from 'yup';
import { useGetFooterQuery } from '../../../store/features/api/homeApi';
import { useSubscribeMutation } from '../../../store/features/api/userApi';
import { translate } from '../../../utils/translate';
import Button from '../../buttons/Button';

const Footer = () => {
    const [subscribe] = useSubscribeMutation();
    const [email, setEmail] = useState('');

    const { data: footerRes } = useGetFooterQuery();

    const validationSchema = yup.object().shape({
        email: yup.string().email().required(),
    });

    const handleSubscribe = async (e: FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        const isValid = validationSchema.isValidSync({ email });

        if (!isValid) {
            toast.error(translate('Invalid email'));
            return;
        }

        try {
            await subscribe(email);
            toast.success(translate('Subscribed'));
            setEmail('');
        } catch (error: any) {
            toast.error(error.data.message);
        }
    };

    return (
        <>
            <section className="mt-3 sm:mt-6 lg:mt-12">
                <div className="theme-container-card no-style">
                    <div className="grid sm:grid-cols-12 gap-9 items-center rounded-md bg-theme-primary/80 text-white max-sm:px-4">
                        <div className="col-span-5 hidden sm:flex items-center justify-end">
                            <img src="/images/mail-wing.png" alt="" />
                        </div>
                        <div className="sm:col-span-6 py-5 sm:py-8 md:py-[60px]">
                            <h2 className="arm-h2 mb-2.5">
                                {translate('Subscribe to our Newsletter')}
                            </h2>
                            <p>
                                {translate(
                                    'ARM Ecommerce Bangladesh is a prominent online shopping site headquartered in Bangladesh with a presence across various Asian countries',
                                )}
                            </p>

                            <form
                                className="mt-5 flex"
                                onSubmit={handleSubscribe}
                            >
                                <input
                                    type="text"
                                    name="email"
                                    value={email}
                                    placeholder={translate('Email Address')}
                                    onChange={(e) => setEmail(e.target.value)}
                                    className="h-9 rounded-l-md pl-5 bg-transparent border border-r-0 border-white/[.55] placeholder:text-white"
                                />
                                <Button
                                    type="submit"
                                    variant="primary"
                                    className="!h-9 rounded-l-none"
                                >
                                    {translate('subscribe')}
                                </Button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

            <footer className="mt-3 sm:mt-6 lg:mt-12 pt-[85px] bg-slate-950 text-white">
                <div className="container">
                    <div className="grid grid-cols-2 lg:grid-cols-10 max-md:gap-y-8 pb-9">
                        <div className="col-span-2 lg:col-span-3">
                            <Link to="/" className="mb-5 inline-block">
                                <img src={footerRes?.homeFooterLogo} alt="" />
                            </Link>

                            <p className="text-slate-500 mb-1">
                                {translate('Customer Supports')}:
                            </p>
                            <Link
                                to={`tel:${footerRes?.homeFooterCustomerSupport}`}
                                className="text-white text-lg font-semibold mb-4"
                            >
                                {footerRes?.homeFooterCustomerSupport}
                            </Link>

                            <p className="text-gray-400 max-w-[250px]">
                                {footerRes?.homeFooterAddress}
                            </p>

                            <div className="mt-6">
                                <a
                                    href={`mailto:${footerRes?.homeFooterEmail}`}
                                    className="text-white text-lg font-semibold"
                                >
                                    {footerRes?.homeFooterEmail}
                                </a>

                                <div className="flex gap-3 items-center mt-4">
                                    {footerRes?.showHomeFacebookLink ===
                                        '1' && (
                                        <a
                                            href={
                                                footerRes.homeFooterFacebookLink
                                            }
                                            target="_blank"
                                            className="h-8 w-8 rounded-full text-lg flex items-center justify-center bg-[#1877F2]"
                                        >
                                            <RiFacebookLine />
                                        </a>
                                    )}
                                    {footerRes?.showHomeTwitterLink === '1' && (
                                        <a
                                            href={
                                                footerRes.homeFooterTwitterLink
                                            }
                                            target="_blank"
                                            className="h-8 w-8 rounded-full text-lg flex items-center justify-center bg-[#00acee]"
                                        >
                                            <RiTwitterXLine />
                                        </a>
                                    )}
                                    {footerRes?.showHomeInstagramLink ===
                                        '1' && (
                                        <a
                                            href={
                                                footerRes.homeFooterInstagramLink
                                            }
                                            target="_blank"
                                            className="h-8 w-8 rounded-full text-lg flex items-center justify-center bg-[#962fbf]"
                                        >
                                            <RiInstagramLine />
                                        </a>
                                    )}
                                    {footerRes?.showHomeYoutubeLink === '1' && (
                                        <a
                                            href={
                                                footerRes.homeFooterYoutubeLink
                                            }
                                            target="_blank"
                                            className="h-8 w-8 rounded-full text-lg flex items-center justify-center bg-[#CD201F]"
                                        >
                                            <RiYoutubeLine />
                                        </a>
                                    )}
                                    {footerRes?.showHomeLinkedInLink ===
                                        '1' && (
                                        <a
                                            href={
                                                footerRes.homeFooterLinkedInLink
                                            }
                                            target="_blank"
                                            className="h-8 w-8 rounded-full text-lg flex items-center justify-center bg-[#0072b1]"
                                        >
                                            <RiLinkedinLine />
                                        </a>
                                    )}
                                </div>
                            </div>
                        </div>
                        <div className="lg:col-span-2">
                            <h4 className="arm-h2 mb-5">
                                {translate('Top Category')}
                            </h4>

                            <ul className="space-y-3">
                                {footerRes?.topCategories.map((category) => (
                                    <li key={category.id}>
                                        <Link
                                            to={`/categories/${category.slug}`}
                                            className="footer-link"
                                        >
                                            {category.name}
                                        </Link>
                                    </li>
                                ))}

                                <li>
                                    <Link
                                        to="/products"
                                        className="inline-flex items-center gap-2 text-theme-secondary hover:text-white"
                                    >
                                        {translate('Browse All Product')}
                                        <span className="text-2xl">
                                            <PiArrowRightLight />
                                        </span>
                                    </Link>
                                </li>
                            </ul>
                        </div>

                        <div className="lg:col-span-2">
                            <h4 className="arm-h2 mb-5">
                                {translate('Quick Links')}
                            </h4>

                            <ul className="space-y-3">
                                {footerRes?.quickLinkPages.map(
                                    (page, index) => (
                                        <li key={`quick-link-${index}`}>
                                            <Link
                                                to={`${page.url}`}
                                                className="footer-link"
                                            >
                                                {page.title}
                                            </Link>
                                        </li>
                                    ),
                                )}
                            </ul>
                        </div>
                        <div className="col-span-2 lg:col-span-3">
                            <h4 className="arm-h2 mb-5">
                                {translate('Popular Tag')}
                            </h4>

                            <ul className="flex flex-wrap gap-2">
                                {footerRes?.popularTags.map((tag) => (
                                    <li key={tag}>
                                        <Link
                                            to={`/products?tag=${tag}`}
                                            state={{
                                                scrollToTop: true,
                                            }}
                                            className="flex items-center justify-center h-6 sm:h-8 px-2 sm:px-3 py-1 border border-neutral-700 hover:bg-neutral-700 hover:border-white"
                                        >
                                            #{tag}
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    </div>

                    <div className="flex max-lg:flex-col gap-y-5 items-center justify-between border-t border-[#eee]/[.14] py-6">
                        <div className="flex max-sm:flex-wrap gap-x-4 md:gap-x-10 gap-y-2">
                            {footerRes?.defaultPages.map((page) => (
                                <Link
                                    to={`/pages/${page.slug}`}
                                    className="text-neutral-400 hover:text-white"
                                    key={page.id}
                                >
                                    {page.title}
                                </Link>
                            ))}
                        </div>

                        {footerRes?.showHomeFooterPlayStoreLink === '1' ||
                        footerRes?.showHomeFooterAppStoreLink === '1' ? (
                            <div className="flex max-sm:flex-wrap items-center lg:justify-end gap-3">
                                <p className="pr-8 max-sm:w-full">
                                    {translate('Download App')}
                                </p>
                                {footerRes?.showHomeFooterPlayStoreLink ===
                                    '1' && (
                                    <Link
                                        to={footerRes?.homeFooterPlayStoreLink}
                                        className="max-sm:grow flex items-center bg-theme-primary px-5 py-4 gap-3 rounded"
                                    >
                                        <span className="text-2xl">
                                            <BsGooglePlay />
                                        </span>
                                        <span className="space-y-1">
                                            <p className="text-[11px] leading-none">
                                                {translate('Get it now')}
                                            </p>
                                            <p className="leading-none font-semibold">
                                                Google Play
                                            </p>
                                        </span>
                                    </Link>
                                )}
                                {footerRes?.showHomeFooterAppStoreLink ===
                                    '1' && (
                                    <Link
                                        to={footerRes?.homeFooterAppStoreLink}
                                        className="max-sm:grow flex items-center bg-theme-primary px-5 py-4 gap-3 rounded"
                                    >
                                        <span className="text-2xl">
                                            <BsApple />
                                        </span>
                                        <span className="space-y-1">
                                            <p className="text-[11px] leading-none">
                                                {translate('Get it now')}
                                            </p>
                                            <p className="leading-none font-semibold">
                                                App Store
                                            </p>
                                        </span>
                                    </Link>
                                )}
                            </div>
                        ) : null}
                    </div>
                </div>
            </footer>
        </>
    );
};

export default Footer;
