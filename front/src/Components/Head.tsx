import React from 'react';
import Style from '../Styles/Header.module.css';
import vinted from '../assets/Vinted.png';

const Head = () => {
    return (

        <div className={Style.ctn1}>

            <div className={Style.ctn2}>

                <div className={Style.gauche}>

                    <p>menu</p>
                    <p>Recherche</p>
                    <div />

                </div>

                <div className={Style.mid}>

                    <img className={Style.logo} src={vinted} />

                </div>

                <div className={Style.droite}>

                    <p>contactez-nous</p>
                    <p>WhishList</p>
                    <div />

                </div>




            </div>

        </div>



    );
};

export default Head;