import { translate } from '../../utils/translate';

const ReviewReply = () => {
    return (
        <div className="bg-stone-50 rounded-md border border-zinc-100 py-3 xl:py-6 px-3 md:px-6 xl:px-9">
            <div className="flex items-center gap-3">
                <div>
                    <img
                        className="rounded-full w-[44px] aspect-square"
                        src="https://via.placeholder.com/44"
                    />
                </div>
                <div>
                    <div className="text-sm font-public-sans">
                        {translate('Response from seller')}
                    </div>
                    <time className="text-theme-secondary-light text-xs">
                        1st December, 2023
                    </time>
                </div>
            </div>
            <div className="text-neutral-500 text-sm mt-3">
                ARM Ecommerce Bangladesh is a prominent online shopping site
                headquartered in Bangladesh, with a presence across various
                Asian countries, including Singapore
            </div>
        </div>
    );
};

export default ReviewReply;
