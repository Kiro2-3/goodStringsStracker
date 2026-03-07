import Checkbox from '@/Components/Checkbox';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Head, Link, useForm } from '@inertiajs/react';
import '../../../css/login.css';

// Modern minimalist login page component.
// Props are injected by the server-side controller (status & canResetPassword).
export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <div className="login-bg min-h-screen flex flex-col items-center justify-center" style={{ background: 'linear-gradient(135deg, #e889f7 0%, #ffd429c7 100%)', color: '#111' }}>
            <Head title="Log in" />
            <div className="w-full max-w-5xl flex flex-row items-center justify-center gap-0 md:gap-12 bg-transparent">
                {/* Logo section (left) */}

                <div className="hidden md:flex flex-1 flex-col items-center justify-center h-full">
                    <img src="/images/stracker-logo.png" alt="sTracker Logo" className="mb-8" style={{width: '1000px', height: '700'}} />
                    <div className="login-quote mt-8 text-center text-lg italic" style={{color:'#111', maxWidth:'420px'}}>
                        “What you track, you improve. <span className='font-semibold'>sTracker</span> turns your money into measurable progress.”
                    </div>
                </div>

                {/* Login form section (right) */}
                <div className="flex-1 flex flex-col items-center justify-center w-full">
                    <div
                        className="login-card w-full max-w-md"
                        style={{
                            background: 'rgba(255,255,255,0.35)',
                            boxShadow: '0 8px 32px 0 rgba(31, 38, 135, 0.18)',
                            color: '#23272f',
                            borderRadius: '2rem',
                            border: '1.5px solid rgba(255,255,255,0.35)',
                            backdropFilter: 'blur(12px)',
                            WebkitBackdropFilter: 'blur(12px)',
                            padding: '2.5rem 2rem',
                            margin: '0 auto',
                        }}
                    >
                        {status && (
                            <div className="alert alert-success mb-4">
                                <span>{status}</span>
                            </div>
                        )}
                        <form onSubmit={submit} className="login-form">
                            <div>
                                <InputLabel htmlFor="email" value="Email Address" className="login-label" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    name="email"
                                    value={data.email}
                                    autoComplete="username"
                                    isFocused={true}
                                    onChange={(e) => setData('email', e.target.value)}
                                    placeholder="you@example.com"
                                    className="login-input"
                                />
                                <InputError message={errors.email} className="mt-1" />
                            </div>
                            <div>
                                <InputLabel htmlFor="password" value="Password" className="login-label" />
                                <TextInput
                                    id="password"
                                    type="password"
                                    name="password"
                                    value={data.password}
                                    autoComplete="current-password"
                                    onChange={(e) => setData('password', e.target.value)}
                                    placeholder="••••••••"
                                    className="login-input"
                                />
                                <InputError message={errors.password} className="mt-1" />
                            </div>
                            <div className="login-checkbox-row">
                                <Checkbox
                                    id="remember"
                                    name="remember"
                                    checked={data.remember}
                                    onChange={(e) => setData('remember', e.target.checked)}
                                />
                                <label htmlFor="remember" className="login-checkbox-label">
                                    Keep me logged in
                                </label>
                            </div>
                            <PrimaryButton className="login-btn" disabled={processing}>
                                {processing ? 'Signing in...' : 'Sign In'}
                            </PrimaryButton>
                        </form>
                        <div className="login-divider">OR</div>
                        <div className="login-links">
                            {canResetPassword && (
                                <Link href={route('password.request')} className="login-link">
                                    Forgot password?
                                </Link>
                            )}
                            <span>·</span>
                            <Link href={route('register')} className="login-link">
                                Create account
                            </Link>
                        </div>
                    </div>
               
                </div> 
            </div> 
                {/* Quote moved above, nothing here now */}
        </div> 
        
    );
}