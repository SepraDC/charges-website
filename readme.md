<!-- Improved compatibility of back to top link: See: https://github.com/SepraDC/charges-website/pull/73 -->
<a name="readme-top"></a>
[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]


<!-- PROJECT LOGO -->
<br />
<div align="center">
  <h3 align="center">Charge Website</h3>

  <p align="center">
    A charges information website
    <br />
    <a href="https://github.com/SepraDC/charges-website"><strong>Explore the docs »</strong></a>
    <br />
    <br />
    <a href="https://github.com/SepraDC/charges-website">View Demo</a>
    ·
    <a href="https://github.com/SepraDC/charges-website/issues">Report Bug</a>
    ·
    <a href="https://github.com/SepraDC/charges-website/issues">Request Feature</a>
  </p>
</div>



<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->
## About The Project

[![Product Name Screen Shot][product-screenshot]](https://prel.sepradc.ovh)

This website was created to respond to a request I faced. I like to follow my account state and see all my next charges to anticipate my spending money. So I created this little project to add all my charges and set them passed or not.

<p align="right">(<a href="#readme-top">back to top</a>)</p>



### Built With

* [![Nuxt][Nuxt.js]][Nuxt-url]
* [![Tailwind][Tailwind.com]][Tailwind-url]
* [![Symfony][Symfony.com]][Symfony-url]

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- GETTING STARTED -->
## Getting Started

This is an example of how you may give instructions on setting up your project locally.
To get a local copy up and running follow these simple example steps.

### Prerequisites

The project was built with Nuxt 3 and symfony 6, The stack is full dockerized and you just need install docker and if you want use make to run script.

### Installation

1. Clone the repo
   ```sh
   git clone https://github.com/Sepradc/charges-website.git
   ```
2. Run the setup command
    ```sh
    make setup
    ```
3. Add certificates in .docker/caddy/certs in your browser to allow ssl
4. Run make up command
    ```sh
    make up
    ```

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- USAGE EXAMPLES -->
## Usage

You can enter every container with make command
```sh
make api ## enter api container
make front ## enter front container
```

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- DEPLOYMENT -->
## Deployment

Production runs as the `charges-website` stack in Portainer, deployed from `compose-prod.yml` on `main`.

1. Set the stack environment variables in Portainer from [`.env.secrets.dist`](.env.secrets.dist).
   `APP_SECRET` and `JWT_PASSPHRASE` are mandatory: compose and the api entrypoint refuse to start without them, or with the public defaults from `api/.env`.
2. Put the stack webhook URL (Portainer → stack → GitOps updates) in a git-ignored `.portainer-webhook` file at the repo root.
3. Build, push and redeploy: `./build-and-push.sh`. It triggers the webhook once the images are pushed; Portainer then pulls `main` and the new images.

Changes to `compose-prod.yml` alone are applied with "Pull and redeploy" in Portainer (or by calling the webhook).

The JWT keypair is never shipped in the image. It is generated on first start with `JWT_PASSPHRASE` and persisted in the `jwt_keys` volume.

### Rotating secrets

If a secret leaks (or the JWT key was ever shipped in an image):

1. Set new `APP_SECRET` and `JWT_PASSPHRASE` values in the Portainer stack environment (`openssl rand -hex 32`)
2. Stop the stack in Portainer and drop the keypair so a new one is generated: `docker volume rm charges-website_jwt_keys`
3. Start the stack again. Every user is logged out, since existing JWTs and remember-me cookies become invalid.
4. Delete the leaked images from the registry and run its garbage collector.

<p align="right">(<a href="#readme-top">back to top</a>)</p>


<!-- LICENSE -->
## License

Distributed under the MIT License. See `LICENSE.txt` for more information.

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- CONTACT -->
## Contact

Lucas Birac - [@SepraDC](https://twitter.com/SepraDC)

Project Link: [https://github.com/SepraDC/charges-website](https://github.com/SepraDC/charges-website)

<p align="right">(<a href="#readme-top">back to top</a>)</p>



[contributors-shield]: https://img.shields.io/github/contributors/SepraDC/charges-website.svg?style=for-the-badge
[contributors-url]: https://github.com/SepraDC/charges-website/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/SepraDC/charges-website.svg?style=for-the-badge
[forks-url]: https://github.com/SepraDC/charges-website/network/members
[stars-shield]: https://img.shields.io/github/stars/SepraDC/charges-website.svg?style=for-the-badge
[stars-url]: https://github.com/SepraDC/charges-website/stargazers
[issues-shield]: https://img.shields.io/github/issues/SepraDC/charges-website.svg?style=for-the-badge
[issues-url]: https://github.com/SepraDC/charges-website/issues
[license-shield]: https://img.shields.io/github/license/SepraDC/charges-website.svg?style=for-the-badge
[license-url]: https://github.com/SepraDC/charges-website/blob/master/LICENSE.txt
[product-screenshot]: docs/images/charges.png
[Nuxt.js]: https://img.shields.io/badge/nuxtjs-grey?style=for-the-badge&logo=nuxt.js
[Nuxt-url]: https://nuxt.com
[Tailwind.com]: https://img.shields.io/badge/Tailwind-white?style=for-the-badge&logo=tailwindcss&logoColor=blue
[Tailwind-url]: https://tailwindcss.com/
[Symfony.com]: https://img.shields.io/badge/Symfony-1F2937?style=for-the-badge&logo=symfony
[Symfony-url]: https://nuxt.com
