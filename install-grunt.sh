#!/usr/bin/env bash

#== Bash helpers ==

function info {
  echo " "
  echo "--> $1"
  echo " "
}

#== Provision script ==

info "Provision-script user: `whoami`"

info "Install Ruby required packages"
sudo apt-get install -y git-core curl zlib1g-dev build-essential libssl-dev libreadline-dev libyaml-dev libsqlite3-dev sqlite3 libxml2-dev libxslt1-dev libcurl4-openssl-dev python-software-properties libffi-dev

info "Install imagemagick for favicon grunt task"
sudo apt-get install -y imagemagick

info "Install NodeJS v0.12.18"
npmBin=$(echo "/usr/lib/nodejs/node-v0.12.18/bin/npm")

sudo rm -rf /usr/lib/nodejs
sudo mkdir /usr/lib/nodejs
curl -so node-v0.12.18-linux-x64.tar.gz https://nodejs.org/dist/v0.12.18/node-v0.12.18-linux-x64.tar.gz
sudo tar -xzf node-v0.12.18-linux-x64.tar.gz -C /usr/lib/nodejs
rm node-v0.12.18-linux-x64.tar.gz
sudo mv /usr/lib/nodejs/node-v0.12.18-linux-x64/ /usr/lib/nodejs/node-v0.12.18
echo 'export NODEJS_HOME=/usr/lib/nodejs/node-v0.12.18' >> /home/vagrant/.bashrc
echo 'export PATH="$NODEJS_HOME/bin:$PATH"' >> /home/vagrant/.bashrc

info "Install NPM dependencies"
${npmBin} install

info "Install Grunt cli"
sudo ${npmBin} install -g grunt-cli

info "Install Ruby"
rbEnvBin=$(echo "/home/vagrant/.rbenv/bin/rbenv")
gemBin=$(echo "/home/vagrant/.rbenv/shims/gem")

sudo rm -rf ~/.rbenv
git clone https://github.com/rbenv/rbenv.git ~/.rbenv
git clone https://github.com/rbenv/ruby-build.git ~/.rbenv/plugins/ruby-build
echo 'export PATH="$HOME/.rbenv/bin:$PATH"' >> /home/vagrant/.bashrc
echo 'eval "$(rbenv init -)"' >> /home/vagrant/.bashrc
echo 'export PATH="$HOME/.rbenv/plugins/ruby-build/bin:$PATH"' >> /home/vagrant/.bashrc
info "Ruby installation may take a few minutes..."
${rbEnvBin} install 2.4.2
${rbEnvBin} global 2.4.2

${gemBin} install bundler

info "Install Ruby sass gem"
${gemBin} install sass
