export default function InputError({ message, className = '', ...props }) {
    return message ? (
        <div
            {...props}
            className={'alert alert-error mt-2 py-2 ' + className}
        >
            <svg xmlns="http://www.w3.org/2000/svg" className="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 14l-2-2m0 0l-2-2m2 2l2-2m-2 2l-2 2m8-8l2 2m0 0l2 2m-2-2l-2 2m2-2l2-2" />
            </svg>
            <span className="text-sm">{message}</span>
        </div>
    ) : null;
}
