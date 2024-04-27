import Style from '../Styles/Header.module.css';
import Person2OutlinedIcon from '@mui/icons-material/Person2Outlined';
import Menu from './SideBar';
import ShoppingBasketOutlinedIcon from '@mui/icons-material/ShoppingBasketOutlined';
import Button from '@mui/material/Button';
import SearchBar from './Searchbar';
import AddIcon from '@mui/icons-material/Add';
import { Link } from 'react-router-dom';
import vinted from '../assets/Vinted.png';



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
                    <SearchBar onSearch={() => { }} />
                    <div />
                </div>

                <div className={Style.mid}>
                    {/* <img className={Style.logo} src={vinted} /> */}
                </div>

                <div className={Style.droite}>

                    <Link to="/login">
                        <Button variant="text" className={Style.btn} >
                            <Person2OutlinedIcon sx={{ color: '#444' }} />

                            <p className={Style.text}> Compte</p>

                        </Button>
                    </Link>

                    <Link to="/panier">

                        <Button variant="text" className={Style.btn}  >
                            <ShoppingBasketOutlinedIcon sx={{ color: '#444' }} />
                            <p className={Style.text}> Panier</p>
                        </Button>

                    </Link>

                    <Link to="/addArticle" className={Style.btn}>

                        <Button variant="text" className={Style.btn}  >
                            <AddIcon sx={{ color: '#444' }} />
                            <p className={Style.text}> Ajouter</p>
                        </Button>

                    </Link>




                    <div />

                </div>
            </div>

        </div>


    );
};
