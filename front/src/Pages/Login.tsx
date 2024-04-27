import React, { useState } from 'react';
import Style from '../Styles/Login.module.css';
import TextField from '@mui/material/TextField';
import Button from '@mui/material/Button';
import { Link } from 'react-router-dom';
import axios from 'axios';
import {useAuth} from "../Components/Authentication/AuthProvider";

const Login: React.FC = () => {
    const [login, setLogin] = useState<string>('');
    const [password, setPassword] = useState<string>('');
    const authProvider = useAuth();
    const handleLogin = async (e: React.FormEvent) => {
        e.preventDefault();

        try {
            const response = await axios.post('http://localhost:8000/api/login', { login, password });
            const data = response.data;
            if (response.status === 200) {
                authProvider.onLogin(data.token);
            }
            setLogin('');
            setPassword('');
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
                        <form className={Style.form} onSubmit={(e) => handleLogin(e)}>
                            <div className={Style.input}>
                                <TextField
                                    id="login"
                                    label="Login"
                                    variant="outlined"
                                    type={"text"}
                                    fullWidth
                                    value={login}
                                    required={true}
                                    onChange={(e) => setLogin(e.target.value)}
                                />
                                <TextField
                                    id="password"
                                    label="Password"
                                    type="password"
                                    variant="outlined"
                                    fullWidth
                                    value={password}
                                    required={true}
                                    onChange={(e) => setPassword(e.target.value)}
                                />
                                <div className={Style.btnGroup}>
                                    <Button type={'submit'} variant="text" className={Style.btn}>Connexion</Button>
                                    <Link to="/register">Pas encore de compte ? S'inscrire ici !</Link>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Login;
