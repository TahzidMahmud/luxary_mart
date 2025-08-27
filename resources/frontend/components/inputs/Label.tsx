interface Props extends React.HTMLProps<HTMLLabelElement> {}

const Label = ({ children, className, ...rest }: Props) => {
    return (
        <label
            className={`mb-2 text-[11px] uppercase font-public-sans inline-block ${
                className || ''
            }`}
            {...rest}
        >
            {children}
        </label>
    );
};

export default Label;
