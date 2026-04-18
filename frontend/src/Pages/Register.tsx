import React, {useEffect, useState} from 'react';
import Style from '../Styles/Register.module.css';
import TextField from '@mui/material/TextField';
import Button from '@mui/material/Button';
import {Link} from 'react-router-dom';
import axios from 'axios';
import {useAuth} from "../Components/Authentication/AuthProvider";
import {Alert} from "@mui/material";

export type ErrorType = {
    isError: boolean,
    message: string,
    type: string
}

const Register: React.FC = () => {
    const [email, setEmail] = useState<string>('');
    const [password, setPassword] = useState<string>('');
    const [firstname, setFirstname] = useState<string>('');
    const [lastname, setLastname] = useState<string>('');
    const [confirmPassword, setConfirmPassword] = useState<string>('');
    const [login, setLogin] = useState<string>('');
    const [passwordError, setPasswordError] = useState<ErrorType>(
        {
            isError: false,
            message: '',
            type: 'password',
        }
    )
    const [error, setError] = useState<ErrorType>(
        {
            isError: false,
            message: '',
            type: '',
        }
    )
    const authProvider = useAuth();

    const handleRegister = async (e: React.FormEvent) => {
        e.preventDefault();
        setError({
            isError: false,
            message: '',
            type: '',
        })
        setPasswordError({
            isError: false,
            message: '',
            type: 'password',
        })
        if (password !== confirmPassword) {
            setPasswordError({
                isError: true,
                message: 'Les mots de passe ne correspondent pas !',
                type: 'password',
            });
            return null;
        }
        try {
            const response = await axios.post('http://localhost:8000/api/register', {
                email,
                password,
                login,
                firstname,
                lastname
            });
            const data = response.data;
            if (response.status === 201) {
                authProvider.onLogin(data.token);
            }
            setEmail('');
            setPassword('');
            setLastname('');
            setFirstname('');
            setLogin('');
            setConfirmPassword('');
        } catch (data: any) {
            if (data.response.data.message) {
                setError({
                    isError: true,
                    message: data.response.data.message,
                    type: data.response.data.type
                })
            }
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
                            <p className={Style.text}>
                                Veuillez ajouter votre e-mail ainsi que votre mot de passe
                                ci-dessous pour accéder à votre compte ou créer un compte
                            </p>
                        </div>
                        <form onSubmit={(e) => handleRegister(e)}>
                            <div className={Style.form}>
                                <div className={Style.inputGroup}>
                                    <TextField
                                        id="firstname"
                                        label="Prénom"
                                        variant="outlined"
                                        value={firstname}
                                        onChange={(e) => setFirstname(e.target.value)}
                                        required
                                    />
                                    <TextField
                                        id="lastname"
                                        label="Nom"
                                        type="text"
                                        variant="outlined"
                                        value={lastname}
                                        onChange={(e) => setLastname(e.target.value)}
                                        required
                                    />
                                    <TextField
                                        id="email"
                                        label="Email"
                                        type={"email"}
                                        variant="outlined"
                                        value={email}
                                        onChange={(e) => setEmail(e.target.value)}
                                        required
                                        error={error.isError && error.type === 'email'}
                                    />
                                </div>
                                <div className={Style.inputGroup}>
                                    <TextField
                                        id="login"
                                        label="Nom d'utilisateur"
                                        variant="outlined"
                                        value={login}
                                        onChange={(e) => setLogin(e.target.value)}
                                        required
                                        error={error.isError && error.type === 'login'}
                                    />
                                    <TextField
                                        id="password"
                                        label="Mot de passe"
                                        variant="outlined"
                                        type="password"
                                        value={password}
                                        inputProps={{
                                            pattern: '^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[@$!%*?&])[A-Za-z\\d@$!%*?&]{8,}$',
                                            title: 'Votre mot de passe doit contenir au moins 8 caractères ' +
                                                'dont une majuscule, une minuscule, un chiffre et un caractère spécial.'
                                        }}
                                        onChange={(e) => setPassword(e.target.value)}
                                        required
                                        error={passwordError.isError}
                                    />
                                    <TextField
                                        id="confirmPassword"
                                        label="Confirmez votre mot de passe"
                                        type="password"
                                        variant="outlined"
                                        value={confirmPassword}
                                        inputProps={{
                                            pattern: '^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[@$!%*?&])[A-Za-z\\d@$!%*?&]{8,}$',
                                            title: 'Votre mot de passe doit contenir au moins 8 caractères ' +
                                                'dont une majuscule, une minuscule, un chiffre et un caractère spécial.'
                                        }}
                                        onChange={(e) => setConfirmPassword(e.target.value)}
                                        required
                                        error={passwordError.isError}
                                    />
                                </div>
                            </div>
                            {error.isError &&
                                <Alert severity={"error"}>
                                    {error.message}
                                </Alert>
                            }
                            {passwordError.isError &&
                                <Alert severity={"error"}>
                                    {passwordError.message}
                                </Alert>
                            }
                            <div className={Style.btnGroup}>
                                <Button className={Style.btn}
                                        variant="text"
                                        type={'submit'}
                                >
                                    S'inscrire
                                </Button>
                                <Link to="/login">
                                        Déja un compte ? Se connecter ici !
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Register;