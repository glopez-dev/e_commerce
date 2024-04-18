import Head from '../Components/Head';
import Style from '../Styles/Home.module.css';
import Article from '../Components/Article';

/**
 * Renders the Home page component.
 *
 * @return {JSX.Element} The Home page component.
 */
export default function Home(): JSX.Element {

    return (

        <>
            <div className={Style.containers}>

                <Article />
            </div>
        </>
    );
}