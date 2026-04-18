import React from 'react';
import Style from '../../Styles/Order/SuccessOrder.module.css'
import {Link} from "react-router-dom";
import HermHess from '../../assets/logo-hermhess.png';
import Button from "@mui/material/Button";

export default function SuccessOrder(): React.JSX.Element {

    return (
        <div className={Style.container}>
            <div className={Style.card}>
                <div className={Style.cardHeader}>
                    <div className={Style.cardHeaderTitle}>
                        <p>🛍️ HermHess vous remercie pour votre commande ! ✅</p>
                    </div>
                </div>
                <span className={Style.separator}/>
                <div className={Style.cardBody}>
                    <span className={Style.cardBodyText}>
                        Nous tennons à vous remercier de votre commande. Celà nous aide beaucoup dans l'amélioration de
                        notre plateforme. Nous espérons vous revoir bientôt!
                        Si vous souhaitez voir un recapitulatif de votre commande, cliquez <Link
                        to={"/user/orders"}>ici.</Link>
                    </span>
                </div>
                <span className={Style.separator}/>
                <div className={Style.cardFooter}>
                    <Button
                        className={Style.btn}
                        variant="text"
                        type={'submit'}
                        href={'/'}>
                        Retour au menu
                    </Button>
                </div>
            </div>
        </div>
    )
        ;
}

