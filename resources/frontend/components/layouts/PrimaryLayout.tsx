import { Suspense, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { Outlet, useLocation } from 'react-router-dom';
import { setAuthData } from '../../store/features/auth/authSlice';
import { closePopup } from '../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../store/store';
import Popups from '../popups';
import Copyright from './components/Copyright';
import Footer from './components/Footer';
import Header from './components/header-v2';

const PrimaryLayout = () => {
    const { i18n } = useTranslation();
    const dispatch = useAppDispatch();
    const location = useLocation();

    useEffect(() => {
        dispatch(
            setAuthData({
                language: window.config.languages.find(
                    (lang) => lang.code === i18n.language,
                ),
            }),
        );
    }, []);

    useEffect(() => {
        dispatch(closePopup());
    }, [location.pathname]);

    return (
        <>
            <Header />
            <main>
                <Suspense fallback={<></>}>
                    <Outlet />
                </Suspense>
            </main>
            <Footer />
            <Copyright />

            {/* popups */}
            <Popups />
        </>
    );
};

export default PrimaryLayout;
