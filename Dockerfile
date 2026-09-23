FROM php:8.1-cli

# Install Java (OpenJDK) so java -jar command works on the server
RUN apt-get update && apt-get install -y default-jdk

# Set working directory
WORKDIR /var/www/html

# Copy all project files into the container
COPY . /var/www/html

# Expose port for web traffic
EXPOSE 8080

# Run PHP built-in server
CMD ["php", "-S", "0.0.0.0:8080"]
