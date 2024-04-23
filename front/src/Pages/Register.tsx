import React, { useState } from 'react';
import Style from '../Styles/Login.module.css';
import TextField from '@mui/material/TextField';
import Button from '@mui/material/Button';
import { Link } from 'react-router-dom';
import axios from 'axios';

const Register: React.FC = () => {
    const [email, setEmail] = useState<string>('');
    const [password, setPassword] = useState<string>('');
    const [name, setName] = useState<string>('');

    const handleRegister = async () => {
        try {
            const response = await axios.post<{ data: any }>('YOUR_API_LOGIN_ENDPOINT', { email, password, name });// tu changes la route ici 
            console.log(response.data);
            setEmail('');
            setPassword('');
            setName('');
        } catch (error) {
            console.error('Login failed:', error);

        }
    };

    return (
        <div className={Style.containers}>
            <div className={Style.box}>
                <div className={Style.title}>
                    <h1>Votre compte</h1>
                </div>
                <div className={Style.login}>
                    <div className={Style.card}>
                        <div className={Style.p}>
                            <p className={Style.text}>Veuillez ajouter votre e-mail ainsi que votre mot de passe ci-dessous pour accéder à votre compte ou créer un compte </p>
                        </div>
                        <div className={Style.form}>
                            <div className={Style.input}>
                                <TextField
                                    id="name"
                                    label="Name"
                                    variant="outlined"
                                    fullWidth
                                    value={email}
                                    onChange={(e) => setName(e.target.value)}
                                />
                                <TextField
                                    id="email"
                                    label="Email"
                                    variant="outlined"
                                    fullWidth
                                    value={email}
                                    onChange={(e) => setEmail(e.target.value)}
                                />
                                <TextField
                                    id="password"
                                    label="Password"
                                    type="password"
                                    variant="outlined"
                                    fullWidth
                                    value={password}
                                    onChange={(e) => setPassword(e.target.value)}
                                />
                                <Button variant="text" onClick={handleRegister}>S'inscrire</Button>
                                <Link to="/login" className={Style.btn}>
                                    <Button variant="text" fullWidth>
                                        Se connecter
                                    </Button>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Register;