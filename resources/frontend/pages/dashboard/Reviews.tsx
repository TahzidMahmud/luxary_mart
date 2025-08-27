import { FaHeart } from 'react-icons/fa';
import { useSearchParams } from 'react-router-dom';
import Pagination from '../../components/pagination/Pagination';
import { impressions } from '../../components/popups/ReviewModal';
import ReviewItem from '../../components/reviews/ReviewItem';
import { DashboardReviewsSkeleton } from '../../components/skeletons/from-svg';
import SectionTitle from '../../components/titles/SectionTitle';
import { useGetAllReviewOfUserQuery } from '../../store/features/api/reviewApi';
import { useAuth } from '../../store/features/auth/authSlice';
import { translate } from '../../utils/translate';

const Reviews = () => {
    const [searchParams] = useSearchParams();

    const { user } = useAuth();
    const { data: reviewsRes, isLoading } = useGetAllReviewOfUserQuery({
        userId: user!.id,
        page: searchParams.get('page') || 1,
        limit: searchParams.get('limit') || 10,
    });

    if (isLoading) return <DashboardReviewsSkeleton />;

    const reviews = reviewsRes?.reviews.data;
    const pagination = reviewsRes?.reviews.meta;

    const shopImpression = reviewsRes?.shopReviews.data;

    return (
        <div className="grid md:grid-cols-3">
            <div className="md:col-span-3">
                <div className="theme-container-card">
                    <SectionTitle
                        icon={<FaHeart />}
                        title="My Reviews"
                        className="mb-10"
                    />

                    <h4 className="font-public-sans text-sm mb-7 uppercase">
                        {translate('Product Reviews')}
                    </h4>

                    <div className="">
                        {reviews?.length === 0 ? (
                            <p className="text-center py-10">
                                {translate('No reviews')}
                            </p>
                        ) : (
                            reviews?.map((review) => (
                                <div key={review.id}>
                                    <ReviewItem
                                        showProductDetails
                                        review={review}
                                    />
                                    <hr className="border border-zinc-100 mt-8 mb-7" />
                                </div>
                            ))
                        )}
                    </div>

                    <Pagination
                        className="border-none mt-0 pt-0"
                        pagination={pagination}
                        resourceName="Reviews"
                    />
                </div>
            </div>

            <div className="bg-white border hidden border-zinc-100 rounded-md px-8 py-6">
                <h4 className="font-public-sans text-sm mb-9 uppercase">
                    {translate('Seller Reviews')}
                </h4>

                {shopImpression?.length === 0 ? (
                    <p className="text-center py-10">
                        {translate('No reviews')}
                    </p>
                ) : (
                    shopImpression?.map((impression) => (
                        <div key={impression.id}>
                            <div className="flex items-center justify-between gap-3">
                                <div>
                                    <h4 className="text-sm">
                                        {impression.shop.name}
                                    </h4>
                                    <p className="text-theme-secondary-light text-xs mt-1.5">
                                        {impression.createdDate}
                                    </p>
                                </div>

                                <div
                                    className={`flex flex-col items-center gap-1 ${
                                        impression.impression === 'positive' &&
                                        'text-theme-orange'
                                    }`}
                                >
                                    <span className="text-3xl leading-none">
                                        {
                                            impressions.find(
                                                (item) =>
                                                    item.value ===
                                                    impression.impression,
                                            )?.icon
                                        }
                                    </span>
                                    <span className="capitalize">
                                        {impression.impression}
                                    </span>
                                </div>
                            </div>
                            <hr className="border border-theme-primary-14 mt-7 mb-6" />
                        </div>
                    ))
                )}
            </div>
        </div>
    );
};

export default Reviews;
