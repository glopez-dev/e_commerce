import React from 'react';
import Style from '../Styles/Login.module.css';

import TextField from '@mui/material/TextField';
import Button from '@mui/material/Button';

const Register = () => {
    return (
        <div className={Style.containers}>

            <div className={Style.box}>
                <div className={Style.title}>
                    <h1>Votre compte</h1>
                </div>

                <div className={Style.login}>

                    <div className={Style.card}>
                        <div className={Style.p}>
                            <p className={Style.text}>Vous n'avez pas de compte inscrivez-vous. </p>
                        </div>

                        <div className={Style.form}>
                            <div className={Style.input}>
                                <TextField id="outlined-basic" label="Name" variant="outlined" fullWidth />

                                <TextField id="outlined-basic" label="Email" variant="outlined" fullWidth />
                                <TextField id="outlined-basic" label="Password" variant="outlined" fullWidth />
                                <Button variant="text">Inscription</Button>

                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    );
};

export default Register;