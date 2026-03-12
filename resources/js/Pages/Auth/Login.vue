<template>
  <Head title="Log in" />
  <div class="login-bg">
    <div
      v-if="status"
      class="toast toast-top toast-end z-50"
    >
      <div class="alert alert-success" role="alert">
        <span>{{ status }}</span>
      </div>
    </div>

    <div class="login-hero-shell" style="position: relative; overflow: hidden;">
      <span class="hero-sliding-text">sTrike your Finance!</span>
      <header class="login-hero-nav">
        <div class="login-hero-nav-left">
            <img :src="logoUrl" alt="sTracker preview" class="login-hero-logo shadow-md transition-transform duration-200 hover:scale-110" />
          <span class="login-hero-brand font-bold text-lg">sTracker</span>
        </div>
        <div class="login-hero-nav-actions">
          <button
            type="button"
            class="btn btn-sm normal-case font-semibold rounded-lg border-0 text-white px-5 transition-all duration-300 hover:scale-105 hover:shadow-[0_0_18px_3px_rgba(124,58,237,0.4)]"
            style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 60%, #2563eb 100%);"
            @click="openLoginModal"
          >
            Log in
          </button>
        </div>
      </header>

      <main class="login-hero-main">
        <section class="login-hero-grid">
          <div class="login-hero-copy">
            <h1 class="login-hero-title"> 
              <span class="login-hero-title-highlight" style="background: linear-gradient(90deg, #7c3aed, #FFD700); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Strike your Goals!</span>
            </h1>
            <p class="login-hero-subcopy">
              sTracker turns everyday income and expenses into clear, simple insights so you
              can budget with confidence, avoid surprises, and stay on top of your goals.
            </p>
            <div class="login-hero-cta-row">
              <Link :href="route('register')" class="login-hero-primary-cta btn btn-primary btn-lg normal-case transition-all duration-200 hover:scale-105 hover:shadow-lg">Start tracking for free</Link>
            </div>
          </div>

          <div class="login-hero-card-wrapper flex flex-col gap-6">
            <div class="login-hero-preview">
              <div class="login-hero-preview-inner">
                <div class="login-inspiration-dashboard-frame">
                  <img
                    :src="previewUrl"
                    alt="sTracker preview"
                    class="login-inspiration-preview-img"
                  />
                </div>
              </div>
            </div>
          </div>
        </section>
      <div></div>
        <div></div>
        <div></div>
        <section class="mt-32 px-3 pb-10">
          <!-- Feature cards - marquee rows -->
          <div class="feature-marquee-wrapper">
            <!-- Row 1: left to right -->
            <div class="feature-marquee feature-marquee-ltr">
              <div class="feature-marquee-track">
                <!-- original set -->
                <div class="card bg-gray-100 shadow-sm border border-base-200 feature-card hover:shadow-lg hover:-translate-y-1 hover:border-primary/40">
                  <div class="card-body py-4 px-5">
                    <h3 class="card-title text-sm font-semibold mb-1 text-black flex items-center gap-1.5">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                      Secure & private
                    </h3>
                    <p class="text-xs text-base-content/80">Your data is encrypted in transit and stored safely so only you can see your numbers.</p>
                  </div>
                </div>
                <div class="card bg-gray-100 shadow-sm border border-base-200 feature-card hover:shadow-lg hover:-translate-y-1 hover:border-primary/40">
                  <div class="card-body py-4 px-5">
                    <h3 class="card-title text-sm font-semibold mb-1 text-black flex items-center gap-1.5">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                      Works across devices
                    </h3>
                    <p class="text-xs text-base-content/80">Log in from desktop or mobile and pick up exactly where you left off.</p>
                  </div>
                </div>
                <div class="card bg-gray-100 shadow-sm border border-base-200 feature-card hover:shadow-lg hover:-translate-y-1 hover:border-primary/40">
                  <div class="card-body py-4 px-5">
                    <h3 class="card-title text-sm font-semibold mb-1 text-black flex items-center gap-1.5">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                      Export when you need
                    </h3>
                    <p class="text-xs text-base-content/80">Download your transactions for sharing with your accountant or for backup.</p>
                  </div>
                </div>
                <!-- duplicate set for seamless loop -->
                <div class="card bg-gray-100 shadow-sm border border-base-200 feature-card hover:shadow-lg hover:-translate-y-1 hover:border-primary/40">
                  <div class="card-body py-4 px-5">
                    <h3 class="card-title text-sm font-semibold mb-1 text-black flex items-center gap-1.5">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                      Secure & private
                    </h3>
                    <p class="text-xs text-base-content/80">Your data is encrypted in transit and stored safely so only you can see your numbers.</p>
                  </div>
                </div>
                <div class="card bg-gray-100 shadow-sm border border-base-200 feature-card hover:shadow-lg hover:-translate-y-1 hover:border-primary/40">
                  <div class="card-body py-4 px-5">
                    <h3 class="card-title text-sm font-semibold mb-1 text-black flex items-center gap-1.5">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                      Works across devices
                    </h3>
                    <p class="text-xs text-base-content/80">Log in from desktop or mobile and pick up exactly where you left off.</p>
                  </div>
                </div>
                <div class="card bg-gray-100 shadow-sm border border-base-200 feature-card hover:shadow-lg hover:-translate-y-1 hover:border-primary/40">
                  <div class="card-body py-4 px-5">
                    <h3 class="card-title text-sm font-semibold mb-1 text-black flex items-center gap-1.5">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                      Export when you need
                    </h3>
                    <p class="text-xs text-base-content/80">Download your transactions for sharing with your accountant or for backup.</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Row 2: right to left -->
            <div class="feature-marquee feature-marquee-rtl">
              <div class="feature-marquee-track">
                <!-- original set -->
                <div class="card bg-gray-100 shadow-sm border border-base-200 feature-card hover:shadow-lg hover:-translate-y-1 hover:border-primary/40">
                  <div class="card-body py-4 px-5">
                    <h3 class="card-title text-sm font-semibold mb-1 text-black flex items-center gap-1.5">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                      Visual insights
                    </h3>
                    <p class="text-xs text-base-content/80">See where your money goes with clear charts and category breakdowns every month.</p>
                  </div>
                </div>
                <div class="card bg-gray-100 shadow-sm border border-base-200 feature-card hover:shadow-lg hover:-translate-y-1 hover:border-primary/40">
                  <div class="card-body py-4 px-5">
                    <h3 class="card-title text-sm font-semibold mb-1 text-black flex items-center gap-1.5">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                      Real-time tracking
                    </h3>
                    <p class="text-xs text-base-content/80">Add income or expenses in seconds and watch your balance update instantly.</p>
                  </div>
                </div>
                <div class="card bg-gray-100 shadow-sm border border-base-200 feature-card hover:shadow-lg hover:-translate-y-1 hover:border-primary/40">
                  <div class="card-body py-4 px-5">
                    <h3 class="card-title text-sm font-semibold mb-1 text-black flex items-center gap-1.5">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                      Goal-oriented
                    </h3>
                    <p class="text-xs text-base-content/80">Set spending limits per category and get a clear picture of how well you're sticking to them.</p>
                  </div>
                </div>
                <!-- duplicate set for seamless loop -->
                <div class="card bg-gray-100 shadow-sm border border-base-200 feature-card hover:shadow-lg hover:-translate-y-1 hover:border-primary/40">
                  <div class="card-body py-4 px-5">
                    <h3 class="card-title text-sm font-semibold mb-1 text-black flex items-center gap-1.5">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                      Visual insights
                    </h3>
                    <p class="text-xs text-base-content/80">See where your money goes with clear charts and category breakdowns every month.</p>
                  </div>
                </div>
                <div class="card bg-gray-100 shadow-sm border border-base-200 feature-card hover:shadow-lg hover:-translate-y-1 hover:border-primary/40">
                  <div class="card-body py-4 px-5">
                    <h3 class="card-title text-sm font-semibold mb-1 text-black flex items-center gap-1.5">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                      Real-time tracking
                    </h3>
                    <p class="text-xs text-base-content/80">Add income or expenses in seconds and watch your balance update instantly.</p>
                  </div>
                </div>
                <div class="card bg-gray-100 shadow-sm border border-base-200 feature-card hover:shadow-lg hover:-translate-y-1 hover:border-primary/40">
                  <div class="card-body py-4 px-5">
                    <h3 class="card-title text-sm font-semibold mb-1 text-black flex items-center gap-1.5">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                      Goal-oriented
                    </h3>
                    <p class="text-xs text-base-content/80">Set spending limits per category and get a clear picture of how well you're sticking to them.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- How it works -->
          <div class="mt-14">
            <h2 class="text-xl font-bold text-base-content mb-6 text-center">How it works</h2>
            <div class="grid gap-6 md:grid-cols-3">
              <div class="flex flex-col items-center text-center gap-3 transition-transform duration-200 hover:-translate-y-1 cursor-default">
                <div class="badge badge-primary badge-lg text-lg font-bold w-12 h-12 rounded-full flex items-center justify-center transition-transform duration-200 hover:scale-110">1</div>
                <h4 class="font-semibold text-base-content">Create your account</h4>
                <p class="text-xs text-base-content/60">Sign up for free in under a minute — no credit card required.</p>
              </div>
              <div class="flex flex-col items-center text-center gap-3 transition-transform duration-200 hover:-translate-y-1 cursor-default">
                <div class="badge badge-primary badge-lg text-lg font-bold w-12 h-12 rounded-full flex items-center justify-center transition-transform duration-200 hover:scale-110">2</div>
                <h4 class="font-semibold text-base-content">Log your transactions</h4>
                <p class="text-xs text-base-content/60">Add income and expenses with categories, dates, and descriptions.</p>
              </div>
              <div class="flex flex-col items-center text-center gap-3 transition-transform duration-200 hover:-translate-y-1 cursor-default">
                <div class="badge badge-primary badge-lg text-lg font-bold w-12 h-12 rounded-full flex items-center justify-center transition-transform duration-200 hover:scale-110">3</div>
                <h4 class="font-semibold text-base-content">Track & improve</h4>
                <p class="text-xs text-base-content/60">View charts, spot patterns, and make smarter financial decisions.</p>
              </div>
            </div>
          </div>

          <!-- Testimonials -->
          <div class="mt-14">
            <h2 class="text-xl font-bold text-base-content mb-6 text-center">What users say</h2>
            <div class="grid gap-4 md:grid-cols-3">
              <div class="card bg-base-100 border border-base-200 shadow-sm transition-all duration-200 hover:shadow-md hover:-translate-y-1 hover:border-primary/30 cursor-default">
                <div class="card-body py-5 px-5 gap-3">
                  <p class="text-xs text-base-content/80 italic">"sTracker completely changed how I manage my monthly budget. I finally know where my money goes!"</p>
                  <div class="flex items-center gap-2 mt-1">
                    <div class="avatar placeholder"><div class="bg-primary text-primary-content rounded-full w-7 text-xs font-bold flex items-center justify-center">J</div></div>
                    <div>
                      <p class="text-xs font-semibold text-base-content">Justine Norie.</p>
                      <p class="text-[10px] text-base-content/50">Software developer</p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card bg-base-100 border border-base-200 shadow-sm transition-all duration-200 hover:shadow-md hover:-translate-y-1 hover:border-primary/30 cursor-default">
                <div class="card-body py-5 px-5 gap-3">
                  <p class="text-xs text-base-content/80 italic">"Simple, clean, and fast. I track all my side-hustle income here. The CSV export is super handy."</p>
                  <div class="flex items-center gap-2 mt-1">
                    <div class="avatar placeholder"><div class="bg-secondary text-secondary-content rounded-full w-7 text-xs font-bold flex items-center justify-center">J</div></div>
                    <div>
                      <p class="text-xs font-semibold text-base-content">John Brian Arce</p>
                      <p class="text-[10px] text-base-content/50">Software developer</p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card bg-base-100 border border-base-200 shadow-sm transition-all duration-200 hover:shadow-md hover:-translate-y-1 hover:border-primary/30 cursor-default">
                <div class="card-body py-5 px-5 gap-3">
                  <p class="text-xs text-base-content/80 italic">"I love the charts. Seeing my expenses broken down by category motivates me to spend less."</p>
                  <div class="flex items-center gap-2 mt-1">
                    <div class="avatar placeholder"><div class="bg-accent text-accent-content rounded-full w-7 text-xs font-bold flex items-center justify-center">J</div></div>
                    <div>
                      <p class="text-xs font-semibold text-base-content">Jorie Visaya</p>
                      <p class="text-[10px] text-base-content/50">Software developer</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

      </main>

      <div
        v-if="showLoginModal"
        class="modal modal-open"
        @click.self="closeLoginModal"
      >
        <div class="modal-box w-full max-w-md relative rounded-3xl shadow-2xl">
          <button
            type="button"
            class="btn btn-sm btn-circle btn-error absolute right-3 top-3"
            @click="closeLoginModal"
            aria-label="Close login"
          >✕</button>

          <!-- Brand -->
          <div class="flex items-center gap-2 mb-6">
            <img :src="logoUrl" alt="sTracker Logo" class="h-7 w-7 rounded-lg" />
            <span class="font-bold text-base-content">sTracker</span>
          </div>

          <!-- Heading -->
          <h2 class="text-4xl font-extrabold text-base-content leading-tight mb-1">Holla,<br />Welcome Back</h2>
          <p class="text-sm text-base-content/50 mb-7">Hey, welcome back to your special place</p>

          <form @submit.prevent="submit" class="flex flex-col gap-4">
            <!-- Email -->
            <div class="form-control w-full">
              <TextInput
                id="email"
                type="email"
                name="email"
                v-model="form.email"
                autocomplete="username"
                :isFocused="true"
                placeholder="Email address"
                class="input input-bordered w-full"
                required
              />
              <InputError :message="form.errors.email" class="mt-1 text-error text-xs" />
            </div>

            <!-- Password -->
            <div class="form-control w-full">
              <TextInput
                id="password"
                type="password"
                name="password"
                v-model="form.password"
                autocomplete="current-password"
                placeholder="Password"
                class="input input-bordered w-full"
                required
              />
              <InputError :message="form.errors.password" class="mt-1 text-error text-xs" />
            </div>

            <!-- Remember + Forgot -->
            <div class="flex items-center justify-between">
              <label class="label cursor-pointer gap-2 p-0">
                <Checkbox id="remember" name="remember" v-model="form.remember" class="checkbox checkbox-primary checkbox-sm" />
                <span class="label-text">Remember me</span>
              </label>
              <Link
                v-if="canResetPassword"
                :href="route('password.request')"
                class="link link-primary text-sm font-medium"
              >Forgot Password?</Link>
            </div>

            <!-- Submit -->
            <button
              type="submit"
              class="btn btn-block btn-lg mt-1 rounded-xl font-bold tracking-wide border-0 text-white transition-all duration-300 hover:scale-[1.02] hover:shadow-[0_0_24px_4px_rgba(124,58,237,0.45)] disabled:opacity-70 disabled:cursor-not-allowed disabled:scale-100"
              style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 60%, #2563eb 100%);"
              :disabled="form.processing"
            >
              <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
              <template v-else>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M18 12H9m0 0l3-3m-3 3l3 3" />
                </svg>
              </template>
              {{ form.processing ? 'Signing in...' : 'Sign In' }}
            </button>
          </form>

          <!-- Divider + Sign up -->
          <div class="divider text-base-content/40 text-xs mt-6">OR</div>
          <p class="text-sm text-base-content/50 text-center">
            Don't have an account?
            <Link :href="route('register')" class="link link-primary font-semibold">Sign Up</Link>
          </p>
        </div>
        <div class="modal-backdrop bg-black/30 backdrop-blur-sm" @click="closeLoginModal"></div>
      </div>
      <div></div>
        <div></div>
        <div></div>
        <div></div>
      <footer class="mt-12 bg-transparent text-black">
        <div class="max-w-6xl mx-auto px-6 py-10 md:py-12">
          <div class="grid gap-10 md:grid-cols-4">
            <div>
              <div class="flex items-center gap-3 mb-4">
                <div class="avatar placeholder">

                </div>
                <div>
                  <div class="text-sm font-semibold tracking-wide uppercase text-black">Rocky Penamante</div>
                  <p class="text-xs text-black/70">Tech Enthusiast</p>
                </div>
              </div>
            </div>

            <div class="space-y-2">
              <h3 class="text-xs font-semibold tracking-[0.2em] uppercase text-black">Contact</h3>
              <ul class="space-y-1 text-xs text-black/80">
                <li>Eastwood Residence</li>
                <li>Rodriguez, Philippines</li>
                <li><span class="link link-hover">Get in touch</span></li>
              </ul>
            </div>



            <div class="space-y-4">


              <div>
                <h3 class="text-xs font-semibold tracking-[0.2em] uppercase text-black mb-2">Follow</h3>
                <div class="flex items-center gap-2">
                  <button type="button" class="btn btn-xs btn-circle btn-ghost border border-neutral-300 text-black">in</button>
                  <button type="button" class="btn btn-xs btn-circle btn-ghost border border-neutral-300 text-black">X</button>
                  <button type="button" class="btn btn-xs btn-circle btn-ghost border border-neutral-300 text-black">GH</button>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-8 pt-4 border-t border-neutral-200 flex flex-col gap-2 text-[11px] text-black/70 md:flex-row md:items-center md:justify-between">
            <span>© 2026 Rocky Penamante. All rights reserved.</span>
          </div>
        </div>
      </footer>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import logoUrl from '@/../../public/images/stracker-logo.png';
import previewUrl from '@/../../public/images/frontview2.png';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
  status: String,
  canResetPassword: Boolean
});

const form = useForm({
  email: '',
  password: '',
  remember: false
});

const showLoginModal = ref(true);

function openLoginModal() {
  showLoginModal.value = true;
}

function closeLoginModal() {
  showLoginModal.value = false;
}

function submit() {
  form.post('/login', {
    onFinish: () => form.reset('password'),
  });
}
</script>

<style scoped>
@import '../../../css/login.css';


@keyframes slideVertical {
  0% {
    transform: translate(0%, -15%) rotate(-30deg);
    opacity: 0;
  }
  25% {
    opacity: 1;
  }
  80% {
    opacity: 1;
  }
  100% {
    transform: translate(-80%, 500%) rotate(-30deg);
    opacity: 0;
  }
}

.hero-sliding-text {
  position: absolute;
  top: 0;
  right: 15%;
  font-size: 5rem;
  font-weight: bold;
  background: linear-gradient(135deg, #a855f7 0%, #fbbf24 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  white-space: nowrap;
  animation: slideVertical 5s linear infinite;
  z-index: 0;
  pointer-events: none;
}

@media (max-width: 768px) {
  .hero-sliding-text {
    font-size: 2rem;
  }
}
</style>
