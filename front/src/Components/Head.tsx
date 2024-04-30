import Style from '../Styles/Header.module.css';
import Person2OutlinedIcon from '@mui/icons-material/Person2Outlined';
import Menu from './SideBar';
import ShoppingBasketOutlinedIcon from '@mui/icons-material/ShoppingBasketOutlined';
import Button from '@mui/material/Button';
import SearchBar from './Searchbar';
import AddIcon from '@mui/icons-material/Add';
import { Link } from 'react-router-dom';
import HemHess from '../assets/HemHess.png';
import DropDown from './DropDown';



/**
 * Renders the head component.
 *
 * @return {JSX.Element} The head component.
 */
export default function Head(): JSX.Element {
    return (

        <div className={Style.ctn1}>

            <div className={Style.ctn2}>

                {/* <div className={Style.gauche}>

                    <SearchBar onSearch={() => { }} />
                    <div />
                </div> */}

                <Link to="/" >
                    <div className={Style.mid}>
                        <img className={Style.logo} src={HemHess} />
                    </div>
                </Link>

                <div className={Style.droite}>



                    <Link to="/panier">

                        <Button variant="text" className={Style.btn}  >
                            <ShoppingBasketOutlinedIcon sx={{ color: '#444' }} />
                            <p className={Style.text}> Panier</p>
                        </Button>

                    </Link>

                    <Link to="/addArticle" >

                        <Button variant="text" className={Style.btn}  >
                            <AddIcon sx={{ color: '#444', }} />
                            <p className={Style.text}> Ajouter</p>
                        </Button>

                    </Link>

                    <Link to="/login" className={Style.btnn}>

                        <Person2OutlinedIcon sx={{ color: '#444', padding: '6px 8px' }} />
                        <div className={Style.text}>
                            <DropDown />
                        </div>

                    </Link>




                    <div />

                </div>
            </div>

        </div>


    );
};
