const Button = ({ title, onClick }) => {
    return (
        <button
            className='shadow-2xl rounded-xl bg-blue-500 text-white p-2 hover:bg-blue-600'
            onClick={onClick}
        >
            {title}
        </button>
    );
};

export default Button;
