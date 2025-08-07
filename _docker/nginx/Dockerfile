# Use the official lightweight Nginx image based on Alpine Linux
FROM nginx:1.28-alpine

# Copy custom Nginx configuration into the container
COPY ./_docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Set the working directory inside the container
WORKDIR /var/www/html

# Expose port 8000 to the host machine
EXPOSE 8000
