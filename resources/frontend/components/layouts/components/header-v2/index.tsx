import BottomBar from './BottomBar';
import CategoryBar from './CategoryBar';
import Logobar from './Logobar';
import TopBar from './TopBar';

const Header = () => {
    return (
        <>
            <TopBar />
            <Logobar />
            <CategoryBar />
            <BottomBar />
        </>
    );
};

export default Header;
