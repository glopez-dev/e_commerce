import Style from '../Styles/Header.module.css';
import Person2OutlinedIcon from '@mui/icons-material/Person2Outlined';
import Menu from './SideBar';
import ShoppingBasketOutlinedIcon from '@mui/icons-material/ShoppingBasketOutlined';
import Button from '@mui/material/Button';
import SearchBar from './Searchbar';
import { Link } from 'react-router-dom';

const handleSearch = (query: string) => {

    console.log('Query:', query);
};


/**
 * Renders the head component.
 *
 * @return {JSX.Element} The head component.
 */
export default function Head(): JSX.Element {
    return (

        <div className={Style.ctn1}>
            <div className={Style.ctn2}>
                <div className={Style.gauche}>
                    <Menu />
                    <SearchBar onSearch={handleSearch} />
                    <div />
                </div>

                <div className={Style.mid}>
                    {/* <img className={Style.logo} src={vinted} /> */}
                </div>

                <div className={Style.droite}>

                    <Link to="/Login">
                        <Button variant="text" className={Style.btn} >
                            <Person2OutlinedIcon sx={{ color: '#444' }} />

                            <p> Compte</p>

                        </Button>
                    </Link>

                    <Button variant="text" className={Style.btn}  >
                        <ShoppingBasketOutlinedIcon sx={{ color: '#444' }} />
                        <p> Panier</p>
                    </Button>
                    <div />

                </div>
            </div>
        </div>

    );
};
