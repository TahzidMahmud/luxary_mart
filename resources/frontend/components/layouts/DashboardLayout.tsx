import { FaHome } from 'react-icons/fa';
import { Outlet, useLocation, useNavigate } from 'react-router-dom';
import { useAuth } from '../../store/features/auth/authSlice';
import { translate } from '../../utils/translate';
import Breadcrumb from '../Breadcrumb';
import { DashboardSidebarSkeleton } from '../skeletons/from-svg';
import DashboardSidebar, { profileLinks } from './components/DashboardSidebar';

const DashboardLayout = () => {
    const navigate = useNavigate();
    const location = useLocation();
    const { isLoading, user } = useAuth();

    const activeLink = profileLinks.find((item) => {
        const regex = new RegExp(`^${item.urlRegex.source}$`);
        return location.pathname.match(regex);
    });

    const breadcrumbDom = (
        <Breadcrumb
            title={translate('dashboard')}
            navigation={[
                { name: translate('home'), link: '/', icon: <FaHome /> },
                { name: translate('My Account'), link: '/dashboard' },
                { name: translate(activeLink?.name) || '' },
            ]}
        />
    );

    if (isLoading) {
        return (
            <>
                {breadcrumbDom}
                <section className="mt-8">
                    <div className="theme-container-card no-style grid grid-cols-4">
                        <DashboardSidebarSkeleton />
                        <div className="col-span-3">
                            {activeLink!.preloader}
                        </div>
                    </div>
                </section>
            </>
        );
    }

    if (!user) {
        navigate('/');
        return;
    }

    return (
        <>
            {breadcrumbDom}
            <section className="mt-8">
                <div className="theme-container-card no-style grid grid-cols-1 lg:grid-cols-4">
                    <div className="hidden lg:block">
                        <DashboardSidebar />
                    </div>

                    <div className="lg:col-span-3">
                        <Outlet />
                    </div>
                </div>
            </section>
        </>
    );
};

export default DashboardLayout;
