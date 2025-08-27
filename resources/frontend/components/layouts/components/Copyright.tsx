import { useGetFooterQuery } from '../../../store/features/api/homeApi';

const Copyright = () => {
    const { data: footerRes } = useGetFooterQuery();

    return (
        <div className="bg-theme-primary text-white py-4">
            <div className="container flex max-sm:flex-col items-center justify-between">
                <p>{footerRes?.homeFooterCopyrightText}</p>
                <div className="">
                    <img
                        src={footerRes?.homeFooterSecuredPayments}
                        alt=""
                        className="max-h-11"
                    />
                </div>
            </div>
        </div>
    );
};

export default Copyright;
