# Use the official MySQL 8.0 image as the base
FROM mysql:8.4.6

# Copy custom MySQL configuration into the container
COPY ./_docker/mysql/mysql.conf /etc/mysql/conf.d/mysql.conf

# Expose default MySQL port
EXPOSE 3306

# Start the MySQL server when the container launches
CMD ["mysqld"]
