import React from 'react';
import Style from '../Styles/Header.module.css';
import vinted from '../assets/Vinted.png';
import Person2OutlinedIcon from '@mui/icons-material/Person2Outlined';
import Menu from './SideBar';





const Head = () => {
    return (

        <div className={Style.ctn1}>

            <div className={Style.ctn2}>

                <div className={Style.gauche}>

                    <Menu />
                    <p>Menu</p>
                    <div />

                </div>

                <div className={Style.mid}>

                    {/* <img className={Style.logo} src={vinted} /> */}

                </div>

                <div className={Style.droite}>
                    <Person2OutlinedIcon sx={{ color: '#444' }} />
                    <p> compte</p>
                    <p>WhishList</p>

                    <div />

                </div>




            </div>

        </div>



    );
};

export default Head;