import { Link } from 'react-router-dom';
import Image from './Image';

interface Props {
    title: string;
    to: string;
    image: string;
    titleOutside?: boolean;
}

const CategoryCard = ({ title, to, image, titleOutside = false }: Props) => {
    if (titleOutside) {
        return (
            <Link to={to} className="text-center w-full h-full">
                <div className="mx-auto outline outline-1 outline-zinc-100 rounded-md transition-all flex items-center justify-center hover:shadow-lg">
                    <Image src={image} alt={title} className="w-full" />
                </div>
                <h3 className="max-xs:text-xs text-xs sm:text-sm mt-2.5">
                    {title}
                </h3>
            </Link>
        );
    }

    return (
        <Link
            to={to}
            className="outline outline-1 outline-zinc-100 pt-1.5 md:pt-2.5 pb-2 md:pb-4 px-3 text-center transition-all w-full h-full hover:shadow-lg"
        >
            <Image src={image} alt={title} className="mx-auto aspect-square" />
            <h3 className="max-xs:text-xs text-xs sm:text-sm mt-1 md:mt-2">
                {title}
            </h3>
        </Link>
    );
};

export default CategoryCard;
