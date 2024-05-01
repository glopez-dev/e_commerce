# What is a .gitkeep file?

Unlike `.gitignore`, `.gitkeep` is not a part of Git's official documentation.

It's an unofficial convention used by Git users to track empty directories.

Git, by default, does not track empty directories – it doesn't add them to our repository. `.gitkeep` is a way to circumvent this limitation.

In short, we want to convey to Git:

> "Hey, this folder is important, even if it's empty for now

# Why do we need to keep the postgres directory ?

We will use this directory as a volume whe the Dockerized postgres will persist it's data
