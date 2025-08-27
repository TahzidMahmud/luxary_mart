import { useState } from 'react';
import { AiOutlineMinus } from 'react-icons/ai';
import { BsPlusLg } from 'react-icons/bs';
import { IFilterAttribute } from '../../../store/features/api/productApi';
import { URLFilterParams } from '../../../types';
import Checkbox from '../../inputs/Checkbox';
import ColorCheckbox from '../../inputs/ColorCheckbox';

interface Props {
    attribute: IFilterAttribute;
    filters: URLFilterParams;
    handleCheckboxChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
}

const AttributeFilterCard = ({
    attribute,
    filters,
    handleCheckboxChange,
}: Props) => {
    const [isAllValuesShown, setShowAllValues] = useState(false);

    if (!attribute.variationValues.length) return null;

    if (attribute.id === 1) {
        // if attribute id is 1 then it's color attribute

        console.log(attribute.variationValues);
        return (
            <div
                className={`border border-theme-primary-14 bg-white rounded-md mt-2.5 px-4 md:px-8 ${
                    attribute.variationValues?.length > 5
                        ? 'py-3 md:py-5'
                        : 'py-3 md:pt-5 md:pb-7'
                }`}
                key={attribute.id}
            >
                <h5 className="text-sm font-public-sans pb-2 border-b border-theme-primary-14 text-zinc-900 uppercase">
                    {attribute.name}
                </h5>

                <div className="mt-5 flex gap-2 flex-wrap uppercase">
                    {attribute.variationValues
                        ?.slice(0, isAllValuesShown ? undefined : 5)
                        .map((variation, index) => (
                            <ColorCheckbox
                                key={index}
                                label={variation.name}
                                name="variationValueIds"
                                value={variation.id}
                                image={variation.image}
                                checked={filters.variationValueIds
                                    ?.map(Number)
                                    ?.includes(variation.id)}
                                onChange={handleCheckboxChange}
                            />
                        ))}
                </div>

                {attribute.variationValues.length > 5 && (
                    <button
                        className="inline-flex items-center gap-1 text-xs font-bold text-theme-secondary-light !mt-4"
                        onClick={() => setShowAllValues(!isAllValuesShown)}
                    >
                        {isAllValuesShown ? 'Show Less' : 'Show More'}
                        <span>
                            {isAllValuesShown ? (
                                <AiOutlineMinus />
                            ) : (
                                <BsPlusLg />
                            )}
                        </span>
                    </button>
                )}
            </div>
        );
    }

    return (
        <div
            className={`border border-theme-primary-14 bg-white rounded-md mt-2.5 px-8 ${
                attribute.variationValues?.length > 5
                    ? 'py-3 md:py-5'
                    : 'py-3 md:pt-5 md:pb-7'
            }`}
            key={attribute.id}
        >
            <h5 className="text-sm font-public-sans pb-2 border-b border-theme-primary-14 text-zinc-900 uppercase">
                {attribute.name}
            </h5>

            <div className="mt-5 flex flex-col gap-3 uppercase">
                {attribute.variationValues
                    ?.slice(0, isAllValuesShown ? undefined : 5)
                    .map((item) => (
                        <Checkbox
                            name="variationValueIds"
                            label={item.name}
                            key={item.id}
                            value={item.id}
                            checked={filters.variationValueIds
                                ?.map(Number)
                                ?.includes(item.id)}
                            onChange={handleCheckboxChange}
                        />
                    ))}
            </div>

            {attribute.variationValues.length > 5 && (
                <button
                    className="inline-flex items-center gap-1 text-xs font-bold text-theme-secondary-light !mt-4"
                    onClick={() => setShowAllValues(!isAllValuesShown)}
                >
                    {isAllValuesShown ? 'Show Less' : 'Show More'}
                    <span>
                        {isAllValuesShown ? <AiOutlineMinus /> : <BsPlusLg />}
                    </span>
                </button>
            )}
        </div>
    );
};

export default AttributeFilterCard;
